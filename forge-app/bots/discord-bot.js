const { Client, GatewayIntentBits } = require('discord.js')
const axios = require('axios')
const client = new Client({
  intents: [
    GatewayIntentBits.Guilds,
    GatewayIntentBits.GuildMessages,
    GatewayIntentBits.MessageContent
  ]
})

require('dotenv').config()

const discordToken = process.env.DISCORD_BOT_TOKEN
const forgeAppUrl = process.env.APP_URL

/**
 * Sanitize a Discord ID.
 *
 * This function checks if the provided ID is a string consisting only of numbers.
 * If the ID is valid, it returns the ID. Otherwise, it returns null.
 *
 * @param {string} id - The Discord ID to be sanitized.
 * @returns {string|null} - The sanitized Discord ID or null if invalid.
 * @throws {Error} - If the ID is invalid.
 */
function sanitizeDiscordId (id) {
  // Check if the ID is a string consisting only of numbers
  if (typeof id !== 'string' || !/^\d+$/.test(id)) {
    // Throw an error if the ID is invalid
    throw new Error(`Invalid Discord ID: ${id}`)
  }
  // Return the sanitized ID
  return id
}

client.once('ready', () => {
  console.log('Discord Bot is ready!')
})

client.on('messageCreate', async (message) => {
  if (message.author.bot) {
    return // Ignore bot messages
  }

  const messageContent = message.content

  // Handle Bug submissions
  if (messageContent.startsWith('!bug')) {
    const bugDescription = messageContent.replace('!bug', '').trim()

    if (!bugDescription) {
      message.reply('Please provide a description of the bug.')
      return
    }

    try {
      // Call Laravel to create the issue using Laravel's API
      const response = await axios.post(
                `${forgeAppUrl}/discord/create-issue`,
                {
                  description: bugDescription,
                  discordUser: message.author.username
                },
                {
                  headers: {
                    Authorization: `Bearer ${process.env.FORGE_API_TOKEN}` // Optional if you want to use API tokens
                  }
                }
      )

      message.reply(
                `Bug report submitted! Forge Issue ID: ${response.data.id}`
      )
    } catch (error) {
      console.error(error)
      message.reply('There was an error submitting your bug report.')
    }
  }

  // Handle Status check requests
  if (content.startsWith('!status')) {
    const issueId = content.replace('!status', '').trim()

    if (!issueId) {
      message.reply('Please provide the issue ID.')
      return
    }

    try {
      // Call Laravel to get the issue status
      const response = await axios.get(
                `${forgeAppUrl}/discord/issue-status/${issueId}`,
                {
                  headers: {
                    Authorization: `Bearer ${process.env.FORGE_API_TOKEN}`
                  }
                }
      )

      message.reply(
                `Issue #${issueId} is currently: ${response.data.status}`
      )
    } catch (error) {
      console.error(error)
      message.reply('There was an error retrieving the issue status.')
    }
  }
})

client.on('messageReactionAdd', async (reaction, user) => {
  // Additional handlers for other commands if needed
})

/**
 * Send a direct message to a Discord user by their username.
 *
 * @param {string} username - The username of the Discord user.
 * @param {string} content - The content of the message to be sent.
 * @throws {Error} - If there is an error sending the message.
 */
async function sendMessageToUser (username, content) {
  try {
    const guilds = client.guilds.cache

    for (const guild of guilds.values()) {
      const members = await guild.members.fetch()
      const member = members.find((m) => m.user.username === username)

      if (member) {
        try {
          await member.send(content)
        } catch (error) {
          console.error(
                        `Failed to send DM to user: ${error.message}`
          )
        }
        console.log(`Message sent to ${username}: ${content}`)
        return
      }
    }

    console.log(`User ${username} not found.`)
  } catch (error) {
    throw new Error(
            `Error sending message to ${username}: ${error.message}`
    )
  }
}

// Function to notify a Discord user by their Forge user ID
async function notifyDiscordUser (userId, messageContent) {
  try {
    const response = await axios.get(
            `${forgeAppUrl}/discord/user/${userId}`,
            {
              headers: {
                Authorization: `Bearer ${process.env.FORGE_API_TOKEN}`
              }
            }
    )

    if (!response.data?.discord_id) {
      throw new Error(`No Discord ID found for user ${userId}`)
    }

    const discordId = sanitizeDiscordId(response.data.discord_id)
    const user = await client.users.fetch(discordId)
    await user.send(messageContent)

    console.log(`Notification sent to Discord user: ${user.username}`)
  } catch (error) {
    if (error.response?.status === 404) {
      console.error(`User ${userId} not found in Forge`)
    } else if (error.code === 10013) {
      // Discord user not found
      console.error(
                `Discord user not found for ID from Forge user ${userId}`
      )
    } else {
      console.error(`Failed to notify user ${userId}: ${error.message}`)
    }
    throw error // Re-throw to let caller handle the failure
  }
}

/**
 * Fetch the roles of a Discord user by their Discord ID.
 *
 * @param {string} discordId - The Discord ID of the user.
 * @returns {Promise<Array<{id: string, name: string}>>} - A promise that resolves to an array of role objects.
 * @throws {Error} - If the Discord ID is invalid or there is an error fetching the roles.
 */
async function fetchUserRoles (discordId) {
  // sanitizeDiscordId will throw if invalid - no need to check return value
  const sanitizedId = sanitizeDiscordId(discordId)

  const guilds = client.guilds.cache
  for (const guild of guilds.values()) {
    const member = await guild.members.fetch(sanitizedId)
    if (member) {
      return member.roles.cache.map((role) => ({
        id: role.id,
        name: role.name
      }))
    }
  }
  return [] // User not found in any guild
}

async function assignRoleToUser (discordId, roleName) {
  try {
    const sanitizedId = sanitizeDiscordId(discordId)
    const guilds = client.guilds.cache
    for (const guild of guilds.values()) {
      const member = await guild.members.fetch(sanitizedId)
      if (member) {
        const role = guild.roles.cache.find((r) => r.name === roleName)
        if (!role) {
          throw new Error(`Role "${roleName}" not found in guild`)
        }
        await member.roles.add(role)
      }
    }
  } catch (error) {
    throw new Error('Error assigning role:', error.message)
  }
}

// Check command-line arguments to assign role
if (process.argv.length > 2) {
  const discordId = process.argv[2]
  const roleName = process.argv[3]
  assignRoleToUser(discordId, roleName)
    .then(() => process.exit(0))
    .catch(() => process.exit(1))
}

// Login to Discord with your bot token
client.login(discordToken)

// Export the functions to be used in other scripts
module.exports = {
  sendMessageToUser,
  notifyDiscordUser,
  fetchUserRoles,
  assignRoleToUser
}

// End of Discord Bot script
