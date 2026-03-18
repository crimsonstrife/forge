import {
  positionCard,
  positionCenteredCard,
  positionHighlight
} from './positioning'

const PROMPT_SELECTOR = '[data-forge-onboarding-prompt]'

export function ensureTourUi (currentUi) {
  if (currentUi) {
    return currentUi
  }

  const scrim = document.createElement('div')
  scrim.className = 'forge-tour-scrim'

  const highlight = document.createElement('div')
  highlight.className = 'forge-tour-highlight'

  const card = document.createElement('div')
  card.className = 'forge-tour-card'

  document.body.append(scrim, highlight, card)

  return { scrim, highlight, card }
}

export function clearTourUi (ui) {
  if (!ui) {
    cleanupBootstrapModalArtifacts()
    return
  }

  ui.scrim.classList.remove('is-visible')
  ui.highlight.classList.remove('is-visible')
  ui.card.classList.remove('is-visible')
  ui.card.replaceChildren()
  ui.card.removeAttribute('style')

  cleanupBootstrapModalArtifacts()
}

export function showPrompt () {
  const prompt = document.querySelector(PROMPT_SELECTOR)

  if (!prompt) {
    return
  }

  getBootstrap().Modal.getOrCreateInstance(prompt).show()
}

export function hidePrompt () {
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

export function renderAnchoredStep (ui, content, bounds) {
  ui.scrim.classList.remove('is-visible')
  ui.highlight.classList.add('is-visible')
  ui.card.classList.add('is-visible')

  positionHighlight(ui.highlight, bounds)
  renderCard(ui.card, content)
  positionCard(ui.card, bounds, content.placement)
}

export function renderCenteredStep (ui, content) {
  ui.scrim.classList.add('is-visible')
  ui.highlight.classList.remove('is-visible')
  ui.card.classList.add('is-visible')

  renderCard(ui.card, content)
  positionCenteredCard(ui.card)
}

function renderCard (card, content) {
  const fragment = document.createDocumentFragment()
  const header = createElement('div', {
    className: 'd-flex align-items-start justify-content-between gap-3'
  })
  const heading = createElement('div')
  const stepCounter = createElement('p', {
    className: 'forge-tour-step mb-2',
    text: content.stepCounter
  })
  const title = createElement('h2', {
    className: 'h5 mb-2',
    text: content.title
  })

  heading.append(stepCounter, title)
  header.append(
    heading,
    createButton({
      className: 'btn-close',
      control: 'dismiss',
      ariaLabel: content.dismissAriaLabel
    })
  )

  fragment.append(
    header,
    createElement('p', {
      className: 'text-body-secondary mb-3',
      text: content.body
    })
  )

  if (content.note) {
    fragment.append(
      createElement('p', {
        className: 'small text-body-secondary mb-0',
        text: content.note
      })
    )
  }

  const actions = createElement('div', {
    className:
      'd-flex align-items-center justify-content-between flex-wrap gap-2 mt-3'
  })
  const leftActions = createElement('div')
  const rightActions = createElement('div', {
    className: 'd-flex align-items-center gap-2'
  })

  if (content.showBack) {
    leftActions.append(
      createButton({
        className: 'btn btn-outline-secondary btn-sm',
        control: 'prev',
        text: content.backLabel
      })
    )
  }

  rightActions.append(
    createButton({
      className: 'btn btn-link btn-sm text-decoration-none',
      control: 'dismiss',
      text: content.dismissLabel
    }),
    createButton({
      className: 'btn btn-primary btn-sm',
      control: content.primaryControl,
      text: content.primaryLabel
    })
  )

  actions.append(leftActions, rightActions)
  fragment.append(actions)

  card.replaceChildren(fragment)
}

function createButton ({ className, control, text = '', ariaLabel = null }) {
  const button = createElement('button', {
    className,
    attrs: {
      type: 'button'
    }
  })

  if (control) {
    button.dataset.tourControl = control
  }

  if (ariaLabel) {
    button.setAttribute('aria-label', ariaLabel)
  }

  if (text !== '') {
    button.textContent = text
  }

  return button
}

function createElement (tagName, options = {}) {
  const element = document.createElement(tagName)

  if (options.className) {
    element.className = options.className
  }

  if (options.text !== undefined) {
    element.textContent = options.text
  }

  Object.entries(options.attrs ?? {}).forEach(([key, value]) => {
    element.setAttribute(key, value)
  })

  return element
}

function cleanupBootstrapModalArtifacts () {
  const openNonOnboardingModals = Array.from(
    document.querySelectorAll('.modal.show')
  ).filter((modal) => !modal.matches(PROMPT_SELECTOR))

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

function getBootstrap () {
  if (!window.bootstrap) {
    throw new Error('Bootstrap is not available on window.bootstrap.')
  }

  return window.bootstrap
}
