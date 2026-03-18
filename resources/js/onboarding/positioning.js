const TOUR_PADDING = 12
const TOUR_GAP = 16
const VIEWPORT_MARGIN = 16

export function isVisible (element) {
  if (!(element instanceof HTMLElement)) {
    return false
  }

  if (element.getClientRects().length === 0) {
    return false
  }

  return window.getComputedStyle(element).visibility !== 'hidden'
}

export function positionHighlight (highlight, bounds) {
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

export function positionCard (card, bounds, placement) {
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

export function positionCenteredCard (card) {
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

function clamp (value, min, max) {
  return Math.min(Math.max(value, min), max)
}
