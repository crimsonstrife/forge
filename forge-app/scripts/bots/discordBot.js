const { Client, GatewayIntentBits } = require("discord.js");
const axios = require("axios");

const { Client, GatewayIntentBits } = require("discord.js");
const client = new Client({
    intents: [
        GatewayIntentBits.Guilds,
        GatewayIntentBits.GuildMessages,
        GatewayIntentBits.MessageContent,
        GatewayIntentBits.DirectMessages,
    ],
});
require("dotenv").config();

const discordToken = process.env.DISCORD_BOT_TOKEN;
const forgeAppUrl = process.env.APP_URL; // Forge app URL

client.once("ready", () => {
    console.log("Discord Bot is ready!");
});
