const CONFIG_SELECTOR = '[data-forge-onboarding-config]'

const DEFAULT_MESSAGES = {
  stepCounter: 'Step :current of :total',
  next: 'Next',
  back: 'Back',
  finish: 'Finish',
  openPage: 'Open page',
  endTour: 'End tour',
  dismissAriaLabel: 'End tour',
  continueToPage: 'Continue to the next page to keep the tour moving.',
  collapsedNavigationHint:
    'This step may be tucked inside a collapsible navigation area on smaller screens.'
}

export class OnboardingStore {
  constructor () {
    this.config = null
  }

  load () {
    this.config = readConfig()

    return this.config
  }

  isEnabled () {
    return this.config?.enabled === true && this.tour() !== null
  }

  shouldPrompt () {
    return this.config?.shouldPrompt === true
  }

  dismissPrompt () {
    if (this.config) {
      this.config.shouldPrompt = false
    }
  }

  tour () {
    return this.config?.tour ?? null
  }

  state () {
    return this.tour()?.state ?? null
  }

  setState (state) {
    if (this.tour()) {
      this.tour().state = state
    }
  }

  currentRoute () {
    return this.config?.currentRoute ?? null
  }

  steps () {
    return Array.isArray(this.tour()?.steps) ? this.tour().steps : []
  }

  currentStepIndex () {
    const stepCount = this.steps().length

    if (stepCount === 0) {
      return 0
    }

    const rawIndex = this.state()?.lastStep

    if (!Number.isInteger(rawIndex) || rawIndex < 0) {
      return 0
    }

    return Math.min(rawIndex, stepCount - 1)
  }

  currentStep () {
    return this.steps()[this.currentStepIndex()] ?? null
  }

  message (key, replacements = {}) {
    const template = this.config?.messages?.[key] ?? DEFAULT_MESSAGES[key] ?? ''

    return interpolateTemplate(template, replacements)
  }
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

function interpolateTemplate (template, replacements) {
  return Object.entries(replacements).reduce((output, [key, value]) => {
    return output.replaceAll(`:${key}`, String(value))
  }, template)
}
