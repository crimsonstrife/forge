import { startTourRequest, updateTourStateRequest } from './onboarding/api'
import { isVisible } from './onboarding/positioning'
import { OnboardingStore } from './onboarding/store'
import {
  clearTourUi,
  ensureTourUi,
  hidePrompt,
  renderAnchoredStep,
  renderCenteredStep,
  showPrompt
} from './onboarding/ui'

const store = new OnboardingStore()

let ui = null
let expansionAttemptKey = null

document.addEventListener('DOMContentLoaded', initializeOnboarding)
document.addEventListener('livewire:navigated', initializeOnboarding)
document.addEventListener('click', handleOnboardingClick)

window.addEventListener('resize', () => {
  if (store.state()?.status === 'active') {
    renderActiveStep()
  }
})

window.addEventListener(
  'scroll',
  () => {
    if (store.state()?.status === 'active') {
      renderActiveStep()
    }
  },
  true
)

function initializeOnboarding () {
  store.load()
  expansionAttemptKey = null

  if (!store.isEnabled()) {
    hidePrompt()
    clearTourUi(ui)
    return
  }

  if (store.shouldPrompt() && store.state()?.status !== 'active') {
    showPrompt()
  } else {
    hidePrompt()
  }

  if (store.state()?.status === 'active') {
    renderActiveStep()
    return
  }

  clearTourUi(ui)
}

async function handleOnboardingClick (event) {
  if (!(event.target instanceof Element)) {
    return
  }

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
  const activeTour = store.tour()

  if (!activeTour || activeTour.name !== tourName) {
    return
  }

  const response = await startTourRequest(activeTour)

  if (!response?.state) {
    return
  }

  store.setState(response.state)
  store.dismissPrompt()
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

  store.dismissPrompt()
  hidePrompt()
  clearTourUi(ui)
}

async function handleTourControl (control) {
  const stepIndex = store.currentStepIndex()
  const lastIndex = store.steps().length - 1

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
      clearTourUi(ui)
  }
}

async function setActiveStep (index) {
  if (index < 0 || index >= store.steps().length) {
    return
  }

  const response = await updateTourState('set-step', { lastStep: index })

  if (!response?.state) {
    return
  }

  const nextStep = store.steps()[index]

  if (!nextStep) {
    clearTourUi(ui)
    return
  }

  if (store.currentRoute() !== nextStep.route) {
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

  clearTourUi(ui)
}

async function updateTourState (action, payload = {}) {
  const activeTour = store.tour()

  if (!activeTour) {
    return null
  }

  const response = await updateTourStateRequest(activeTour, action, payload)

  if (response?.state) {
    store.setState(response.state)
    if (action !== 'set-step') {
      store.dismissPrompt()
    }
  }

  return response
}

function openCurrentStep () {
  const step = store.currentStep()

  if (!step) {
    clearTourUi(ui)
    return
  }

  if (store.currentRoute() !== step.route) {
    window.location.assign(step.url)
    return
  }

  renderActiveStep()
}

function renderActiveStep () {
  const step = store.currentStep()

  if (!step || store.state()?.status !== 'active') {
    clearTourUi(ui)
    return
  }

  ui = ensureTourUi(ui)

  if (store.currentRoute() !== step.route) {
    expansionAttemptKey = null
    renderCenteredStep(
      ui,
      buildCardContent(step, {
        note: store.message('continueToPage'),
        primaryLabel: store.message('openPage'),
        primaryControl: 'open-current'
      })
    )
    return
  }

  let target = document.querySelector(step.selector)

  if (!isVisible(target) && maybeExpandNavigation()) {
    const stepKey = `${store.tour()?.name}:${store.currentStepIndex()}`

    if (expansionAttemptKey !== stepKey) {
      expansionAttemptKey = stepKey
      window.setTimeout(renderActiveStep, 225)
      return
    }
  }

  target = document.querySelector(step.selector)

  if (!isVisible(target)) {
    renderCenteredStep(
      ui,
      buildCardContent(step, {
        note: store.message('collapsedNavigationHint')
      })
    )
    return
  }

  expansionAttemptKey = null
  target.scrollIntoView({ block: 'center', inline: 'nearest' })

  const bounds = target.getBoundingClientRect()
  renderAnchoredStep(ui, buildCardContent(step), bounds)
}

function maybeExpandNavigation () {
  if (window.innerWidth >= 768) {
    return false
  }

  const navbar = document.getElementById('appNavbar')

  if (!navbar || navbar.classList.contains('show')) {
    return false
  }

  window.bootstrap.Collapse.getOrCreateInstance(navbar, { toggle: false }).show()

  return true
}

function buildCardContent (step, options = {}) {
  const totalSteps = store.steps().length
  const stepIndex = store.currentStepIndex()
  const isLastStep = stepIndex >= totalSteps - 1

  return {
    title: step.title,
    body: step.body,
    placement: step.placement,
    note: options.note ?? null,
    stepCounter: store.message('stepCounter', {
      current: stepIndex + 1,
      total: totalSteps
    }),
    dismissAriaLabel: store.message('dismissAriaLabel'),
    dismissLabel: store.message('endTour'),
    primaryLabel:
      options.primaryLabel ??
      (isLastStep ? store.message('finish') : store.message('next')),
    primaryControl:
      options.primaryControl ?? (isLastStep ? 'finish' : 'next'),
    showBack: stepIndex > 0,
    backLabel: store.message('back')
  }
}
