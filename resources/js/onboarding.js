const CONFIG_SELECTOR = '[data-forge-onboarding-config]'
const PROMPT_SELECTOR = '[data-forge-onboarding-prompt]'
const TOUR_PADDING = 12
const TOUR_GAP = 16
const VIEWPORT_MARGIN = 16

let config = null
let ui = null
let expansionAttemptKey = null

document.addEventListener('DOMContentLoaded', initializeOnboarding)
document.addEventListener('livewire:navigated', initializeOnboarding)
document.addEventListener('click', handleOnboardingClick)

window.addEventListener('resize', () => {
  if (tourState()?.status === 'active') {
    renderActiveStep()
  }
})

window.addEventListener(
  'scroll',
  () => {
    if (tourState()?.status === 'active') {
      renderActiveStep()
    }
  },
  true
)

function initializeOnboarding () {
  config = readConfig()
  expansionAttemptKey = null

  if (!config || config.enabled !== true || !tour()) {
    hidePrompt()
    clearTourUi()
    return
  }

  if (config.shouldPrompt === true && tourState()?.status !== 'active') {
    showPrompt()
  } else {
    hidePrompt()
  }

  if (tourState()?.status === 'active') {
    renderActiveStep()
    return
  }

  clearTourUi()
}

function readConfig () {
  const node = document.querySelector(CONFIG_SELECTOR)

  if (!node) {
    return null
  }

  try {
    return JSON.parse(node.textContent ?? '{}')
  } catch (error) {
    console.error('Unable to parse onboarding config.', error)
    return null
  }
}

function tour () {
  return config?.tour ?? null
}

function tourState () {
  return tour()?.state ?? null
}

function currentRoute () {
  return config?.currentRoute ?? null
}

function steps () {
  return Array.isArray(tour()?.steps) ? tour().steps : []
}

function currentStepIndex () {
  const stepCount = steps().length

  if (stepCount === 0) {
    return 0
  }

  const rawIndex = tourState()?.lastStep

  if (!Number.isInteger(rawIndex) || rawIndex < 0) {
    return 0
  }

  return Math.min(rawIndex, stepCount - 1)
}

function currentStep () {
  return steps()[currentStepIndex()] ?? null
}

function ensureTourUi () {
  if (ui) {
    return ui
  }

  const scrim = document.createElement('div')
  scrim.className = 'forge-tour-scrim'

  const highlight = document.createElement('div')
  highlight.className = 'forge-tour-highlight'

  const card = document.createElement('div')
  card.className = 'forge-tour-card'

  document.body.append(scrim, highlight, card)

  ui = { scrim, highlight, card }

  return ui
}

function clearTourUi () {
  if (!ui) {
    cleanupBootstrapModalArtifacts()
    return
  }

  ui.scrim.classList.remove('is-visible')
  ui.highlight.classList.remove('is-visible')
  ui.card.classList.remove('is-visible')
  ui.card.innerHTML = ''
  ui.card.removeAttribute('style')

  cleanupBootstrapModalArtifacts()
}

function showPrompt () {
  const prompt = document.querySelector(PROMPT_SELECTOR)

  if (!prompt) {
    return
  }

  getBootstrap().Modal.getOrCreateInstance(prompt).show()
}

function hidePrompt () {
  const prompt = document.querySelector(PROMPT_SELECTOR)

  if (!prompt) {
    cleanupBootstrapModalArtifacts()
    return
  }

  const instance = getBootstrap().Modal.getOrCreateInstance(prompt)

  instance.hide()

  window.setTimeout(() => {
    instance.dispose()
    cleanupBootstrapModalArtifacts()
  }, 300)
}

function cleanupBootstrapModalArtifacts () {
  const openNonOnboardingModals = Array.from(
    document.querySelectorAll('.modal.show')
  ).filter((modal) => {
    return !modal.matches(PROMPT_SELECTOR)
  })

  if (openNonOnboardingModals.length > 0) {
    return
  }

  document
    .querySelectorAll('.modal-backdrop')
    .forEach((backdrop) => backdrop.remove())
  document.body.classList.remove('modal-open')
  document.body.style.removeProperty('overflow')
  document.body.style.removeProperty('padding-right')
}

async function handleOnboardingClick (event) {
  const startButton = event.target.closest('[data-start-tour]')
  if (startButton) {
    event.preventDefault()
    await startTour(startButton.dataset.startTour)
    return
  }

  const promptAction = event.target.closest(
    '[data-onboarding-prompt-action]'
  )
  if (promptAction) {
    event.preventDefault()
    await handlePromptAction(promptAction.dataset.onboardingPromptAction)
    return
  }

  const tourControl = event.target.closest('[data-tour-control]')
  if (tourControl) {
    event.preventDefault()
    await handleTourControl(tourControl.dataset.tourControl)
  }
}

async function startTour (tourName) {
  const activeTour = tour()

  if (!activeTour || activeTour.name !== tourName) {
    return
  }

  const response = await requestJson(activeTour.routes.start, 'POST')

  if (!response?.state) {
    return
  }

  activeTour.state = response.state
  config.shouldPrompt = false
  hidePrompt()
  openCurrentStep()
}

async function handlePromptAction (action) {
  if (!['snooze', 'dismiss'].includes(action)) {
    return
  }

  const response = await updateTourState(action)

  if (!response?.state) {
    return
  }

  config.shouldPrompt = false
  hidePrompt()
  clearTourUi()
}

async function handleTourControl (control) {
  const stepIndex = currentStepIndex()
  const lastIndex = steps().length - 1

  switch (control) {
    case 'open-current':
      openCurrentStep()
      return

    case 'prev':
      await setActiveStep(stepIndex - 1)
      return

    case 'next':
      if (stepIndex >= lastIndex) {
        await completeTour(stepIndex)
        return
      }

      await setActiveStep(stepIndex + 1)
      return

    case 'finish':
      await completeTour(stepIndex)
      return

    case 'dismiss':
      await updateTourState('dismiss')
      clearTourUi()
  }
}

async function setActiveStep (index) {
  if (index < 0 || index >= steps().length) {
    return
  }

  const response = await updateTourState('set-step', { lastStep: index })

  if (!response?.state) {
    return
  }

  const nextStep = steps()[index]

  if (!nextStep) {
    clearTourUi()
    return
  }

  if (currentRoute() !== nextStep.route) {
    window.location.assign(nextStep.url)
    return
  }

  renderActiveStep()
}

async function completeTour (stepIndex) {
  const response = await updateTourState('complete', { lastStep: stepIndex })

  if (!response?.state) {
    return
  }

  clearTourUi()
}

async function updateTourState (action, payload = {}) {
  const activeTour = tour()

  if (!activeTour) {
    return null
  }

  const response = await requestJson(activeTour.routes.update, 'PATCH', {
    action,
    ...payload
  })

  if (response?.state) {
    activeTour.state = response.state
    if (action !== 'set-step') {
      config.shouldPrompt = false
    }
  }

  return response
}

function openCurrentStep () {
  const step = currentStep()

  if (!step) {
    clearTourUi()
    return
  }

  if (currentRoute() !== step.route) {
    window.location.assign(step.url)
    return
  }

  renderActiveStep()
}

function renderActiveStep () {
  const step = currentStep()

  if (!step || tourState()?.status !== 'active') {
    clearTourUi()
    return
  }

  if (currentRoute() !== step.route) {
    expansionAttemptKey = null
    renderCenteredStep(step, {
      note: 'Continue to the next page to keep the tour moving.',
      primaryLabel: 'Open page',
      primaryControl: 'open-current'
    })
    return
  }

  let target = document.querySelector(step.selector)

  if (!isVisible(target) && maybeExpandNavigation()) {
    const stepKey = `${tour()?.name}:${currentStepIndex()}`

    if (expansionAttemptKey !== stepKey) {
      expansionAttemptKey = stepKey
      window.setTimeout(renderActiveStep, 225)
      return
    }
  }

  target = document.querySelector(step.selector)

  if (!isVisible(target)) {
    renderCenteredStep(step, {
      note: 'This step may be tucked inside a collapsible navigation area on smaller screens.'
    })
    return
  }

  expansionAttemptKey = null
  target.scrollIntoView({ block: 'center', inline: 'nearest' })

  const bounds = target.getBoundingClientRect()
  renderAnchoredStep(step, bounds)
}

function maybeExpandNavigation () {
  if (window.innerWidth >= 768) {
    return false
  }

  const navbar = document.getElementById('appNavbar')

  if (!navbar || navbar.classList.contains('show')) {
    return false
  }

  getBootstrap()
    .Collapse.getOrCreateInstance(navbar, { toggle: false })
    .show()

  return true
}

function renderAnchoredStep (step, bounds) {
  const shell = ensureTourUi()

  shell.scrim.classList.remove('is-visible')
  shell.highlight.classList.add('is-visible')
  shell.card.classList.add('is-visible')

  positionHighlight(shell.highlight, bounds)
  renderCard(shell.card, step)
  positionCard(shell.card, bounds, step.placement)
}

function renderCenteredStep (step, options = {}) {
  const shell = ensureTourUi()

  shell.scrim.classList.add('is-visible')
  shell.highlight.classList.remove('is-visible')
  shell.card.classList.add('is-visible')

  renderCard(shell.card, step, options)
  positionCenteredCard(shell.card)
}

function renderCard (card, step, options = {}) {
  const totalSteps = steps().length
  const stepIndex = currentStepIndex()
  const isLastStep = stepIndex >= totalSteps - 1
  const primaryLabel =
        options.primaryLabel ?? (isLastStep ? 'Finish' : 'Next')
  const primaryControl =
        options.primaryControl ?? (isLastStep ? 'finish' : 'next')
  const backButton =
        stepIndex > 0
          ? '<button type="button" class="btn btn-outline-secondary btn-sm" data-tour-control="prev">Back</button>'
          : ''
  const note = options.note
    ? `<p class="small text-body-secondary mb-0">${escapeHtml(options.note)}</p>`
    : ''

  card.innerHTML = `
        <div class="d-flex align-items-start justify-content-between gap-3">
            <div>
                <p class="forge-tour-step mb-2">Step ${stepIndex + 1} of ${totalSteps}</p>
                <h2 class="h5 mb-2">${escapeHtml(step.title)}</h2>
            </div>
            <button type="button" class="btn-close" aria-label="End tour" data-tour-control="dismiss"></button>
        </div>
        <p class="text-body-secondary mb-3">${escapeHtml(step.body)}</p>
        ${note}
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mt-3">
            <div>${backButton}</div>
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-link btn-sm text-decoration-none" data-tour-control="dismiss">End tour</button>
                <button type="button" class="btn btn-primary btn-sm" data-tour-control="${primaryControl}">${escapeHtml(primaryLabel)}</button>
            </div>
        </div>
    `
}

function positionHighlight (highlight, bounds) {
  const top = Math.max(bounds.top - TOUR_PADDING, VIEWPORT_MARGIN)
  const left = Math.max(bounds.left - TOUR_PADDING, VIEWPORT_MARGIN)
  const width = Math.min(
    bounds.width + TOUR_PADDING * 2,
    window.innerWidth - left - VIEWPORT_MARGIN
  )
  const height = Math.min(
    bounds.height + TOUR_PADDING * 2,
    window.innerHeight - top - VIEWPORT_MARGIN
  )

  highlight.style.top = `${top}px`
  highlight.style.left = `${left}px`
  highlight.style.width = `${Math.max(width, 48)}px`
  highlight.style.height = `${Math.max(height, 48)}px`
}

function positionCard (card, bounds, placement) {
  card.style.left = '0px'
  card.style.top = '0px'

  const rect = card.getBoundingClientRect()
  const placements = [placement, 'bottom', 'right', 'top', 'left']

  let top = bounds.bottom + TOUR_GAP
  let left = bounds.left

  for (const candidate of placements) {
    const coords = coordinatesForPlacement(candidate, rect, bounds)
    if (fitsViewport(coords, rect)) {
      ({ top, left } = coords)
      break
    }

    ({ top, left } = coords)
  }

  card.style.left = `${clamp(left, VIEWPORT_MARGIN, window.innerWidth - rect.width - VIEWPORT_MARGIN)}px`
  card.style.top = `${clamp(top, VIEWPORT_MARGIN, window.innerHeight - rect.height - VIEWPORT_MARGIN)}px`
}

function positionCenteredCard (card) {
  card.style.left = '0px'
  card.style.top = '0px'

  const rect = card.getBoundingClientRect()
  const left = clamp(
    (window.innerWidth - rect.width) / 2,
    VIEWPORT_MARGIN,
    window.innerWidth - rect.width - VIEWPORT_MARGIN
  )
  const top = clamp(
    (window.innerHeight - rect.height) / 2,
    VIEWPORT_MARGIN,
    window.innerHeight - rect.height - VIEWPORT_MARGIN
  )

  card.style.left = `${left}px`
  card.style.top = `${top}px`
}

function coordinatesForPlacement (placement, rect, bounds) {
  switch (placement) {
    case 'top':
      return {
        top: bounds.top - rect.height - TOUR_GAP,
        left: bounds.left + bounds.width / 2 - rect.width / 2
      }

    case 'left':
      return {
        top: bounds.top + bounds.height / 2 - rect.height / 2,
        left: bounds.left - rect.width - TOUR_GAP
      }

    case 'right':
      return {
        top: bounds.top + bounds.height / 2 - rect.height / 2,
        left: bounds.right + TOUR_GAP
      }

    default:
      return {
        top: bounds.bottom + TOUR_GAP,
        left: bounds.left + bounds.width / 2 - rect.width / 2
      }
  }
}

function fitsViewport (coords, rect) {
  return (
    coords.top >= VIEWPORT_MARGIN &&
        coords.left >= VIEWPORT_MARGIN &&
        coords.top + rect.height <= window.innerHeight - VIEWPORT_MARGIN &&
        coords.left + rect.width <= window.innerWidth - VIEWPORT_MARGIN
  )
}

function isVisible (element) {
  if (!(element instanceof HTMLElement)) {
    return false
  }

  if (element.getClientRects().length === 0) {
    return false
  }

  return window.getComputedStyle(element).visibility !== 'hidden'
}

async function requestJson (url, method, payload = {}) {
  try {
    const response = await fetch(url, {
      method,
      headers: {
        Accept: 'application/json',
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN':
                    document
                      .querySelector('meta[name="csrf-token"]')
                      ?.getAttribute('content') ?? ''
      },
      credentials: 'same-origin',
      body: method === 'GET' ? undefined : JSON.stringify(payload)
    })

    if (!response.ok) {
      throw new Error(
                `Onboarding request failed with status ${response.status}.`
      )
    }

    return await response.json()
  } catch (error) {
    console.error(error)
    return null
  }
}

function clamp (value, min, max) {
  return Math.min(Math.max(value, min), max)
}

function escapeHtml (value) {
  return String(value)
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&#039;')
}

function getBootstrap () {
  if (!window.bootstrap) {
    throw new Error('Bootstrap is not available on window.bootstrap.')
  }

  return window.bootstrap
}
