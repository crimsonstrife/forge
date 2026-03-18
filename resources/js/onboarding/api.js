export async function startTourRequest (tour) {
  if (!tour) {
    return null
  }

  return requestJson(tour.routes.start, 'POST')
}

export async function updateTourStateRequest (tour, action, payload = {}) {
  if (!tour) {
    return null
  }

  return requestJson(tour.routes.update, 'PATCH', {
    action,
    ...payload
  })
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
