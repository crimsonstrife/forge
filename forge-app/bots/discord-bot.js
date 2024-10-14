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

const { exec } = require('child_process')

client.once('ready', () => {
  console.log('Discord Bot is ready!')
})

client.on('messageCreate', async (message) => {
  if (message.author.bot) return // Ignore bot messages

  const content = message.content

  // Handle Bug submissions
  if (content.startsWith('!bug')) {
    const bugDescription = content.replace('!bug', '').trim()

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

// Function to send a direct message to a Discord user
async function sendMessageToUser (username, content) {
  try {
    const guilds = client.guilds.cache

    for (const guild of guilds.values()) {
      const members = await guild.members.fetch()
      const member = members.find((m) => m.user.username === username)

      if (member) {
        await member.send(content) // Send a direct message
        console.log(`Message sent to ${username}: ${content}`)
        return
      }
    }

    console.log(`User ${username} not found.`)
  } catch (error) {
    console.error('Error sending message:', error)
  }
}

// Function to notify a Discord user by their Forge user ID
async function notifyDiscordUser (userId, messageContent) {
  // Fetch user information from Forge
  try {
    const response = await axios.get(
            `${forgeAppUrl}/discord/user/${userId}`,
            {
              headers: {
                Authorization: `Bearer ${process.env.FORGE_API_TOKEN}`
              }
            }
    )

    const discordId = response.data.discord_id

    if (discordId) {
      const user = await client.users.fetch(discordId) // Fetch the user from Discord by their Discord ID
      if (user) {
        await user.send(messageContent) // Send the user a direct message
        console.log(
                    `Notification sent to Discord user: ${user.username}`
        )
      } else {
        console.log(`Discord user not found for ID: ${discordId}`)
      }
    } else {
      console.log(`No linked Discord account for user ID: ${userId}`)
    }
  } catch (error) {
    console.error('Error fetching user data or sending message:', error)
  }
}

if (process.argv.length > 2) {
  const discordUsername = process.argv[2]
  const messageContent = process.argv[3]
  sendMessageToUser(discordUsername, messageContent)
    .then(() => {
      console.log(`Notification sent to ${discordUsername}`)
      process.exit(0) // Exit the process after sending the message
    })
    .catch((err) => {
      console.error('Error sending notification:', err)
      process.exit(1) // Exit with error code
    })
}

// Function to fetch the Roles of a Discord User
async function fetchUserRoles (discordId) {
  try {
    const guilds = client.guilds.cache

    for (const guild of guilds.values()) {
      const member = await guild.members.fetch(discordId)

      if (member) {
        const roles = member.roles.cache.map((role) => ({
          id: role.id,
          name: role.name
        }))

        console.log(`Roles for Discord user ${discordId}:`, roles)
        return roles
      }
    }

    console.log(`Discord user ${discordId} not found.`)
    return []
  } catch (error) {
    console.error('Error fetching roles:', error)
    return []
  }
}

async function assignRoleToUser (discordId, roleName) {
  try {
    const guilds = client.guilds.cache
    for (const guild of guilds.values()) {
      const member = await guild.members.fetch(discordId)
      if (member) {
        // Find the role by name
        const role = guild.roles.cache.find((r) => r.name === roleName)
        if (role) {
          // Assign the role to the member
          await member.roles.add(role)
          console.log(
                        `Role '${roleName}' assigned to ${member.user.username}`
          )
        } else {
          console.log(`Role '${roleName}' not found in guild.`)
        }
      }
    }
  } catch (error) {
    console.error('Error assigning role:', error)
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
