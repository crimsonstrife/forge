import dotenv from "dotenv";
import { exec } from "child_process";
import { promises as fs } from "fs"; // Load the filesystem module

// Load the dotenv module
dotenv.config();

// Call start
start();

async function backupFile() {
    const $bakProcess = await fs.copyFile(".npmrc", ".npmrc.bak");

    //check if the file was backed up successfully
    if ($bakProcess) {
        console.log("Existing .npmrc file backed up.");

        //return true if the file was backed up successfully.
        return true;
    }

    //return false if the file was not backed up successfully.
    return false;
}

async function createCredentialFile() {
    //set a constant variable to hold the contents of the npmrc file that will be created, leave the token blank for now.
    const npmrc =
        `@awesome.me:registry=https://npm.fontawesome.com/
@fortawesome:registry=https://npm.fontawesome.com/
//npm.fontawesome.com/:_authToken=` +
        process.env.FONTAWESOME_PACKAGE_TOKEN +
        ``;

    /*     //if there is an existing npmrc file, back it up before creating a new one.
    try {
        try {
            await fs.access(".npmrc");

            //check if the file was accessed successfully.
        } catch (error) {
            // File does not exist, handle the error
            console.log("File does not exist:", error);
            return false;
        }
        //continue with the code

        //backup the existing npmrc file
        // skipcq: JS-0242
        const $bakProcess = await backupFile();

        //return false if the file was not backed up successfully.
        if (!$bakProcess) {
            console.log(
                "Existing .npmrc file exists, and could not be backed up."
            );
            return false;
        }

        console.log("Existing .npmrc file exists, and was backed up.");
    } catch (error) {
        // File does not exist, handle the error
        console.log("File does not exist:", error);
    } */

    //create the new npmrc file with the token blanked out.
    // skipcq: JS-0242
    const $fileCreated = await makeFile(npmrc);

    //return true if the file was created successfully.
    return $fileCreated;
}

async function makeFile($npmrc) {
    // Load the filesystem module
    //create a temporary npmrc file with the token, and notify when the file is created.
    // skipcq: JS-0242
    const $fileProcess = await fs.writeFile(".npmrc", $npmrc, "utf8");

    if ($fileProcess) {
        console.log("Temporary .npmrc file creation.");
    }

    //check if the file was created successfully.
    try {
        await fs.access(".npmrc");
    } catch (error) {
        // File does not exist, handle the error
        console.log("File does not exist or could not be accessed:", error);
        return false;
    }

    //return true if the file was created successfully.
    return true;
}

async function start() {
    if (
        process.env.FONTAWESOME_PACKAGE_TOKEN !== undefined &&
        process.env.FONTAWESOME_PACKAGE_TOKEN !== "" &&
        process.env.FONTAWESOME_PACKAGE_TOKEN !== null
    ) {
        //create the npmrc file
        // skipcq: JS-0242
        const $fileCreated = await createCredentialFile(); //check if the file was created successfully

        if ($fileCreated) {
            //set the token in the npmrc file
            // skipcq: JS-0242
            // const $tokenSet = await setToken(
            //     process.env.FONTAWESOME_PACKAGE_TOKEN
            // );

            // if ($tokenSet) {
            //     // skipcq: JS-0242
            //     const proKitInstalled = tryProKitInstall();
            //     if (!proKitInstalled) {
            //         // skipcq: JS-0242
            //         const proInstalled = tryFullProInstall();
            //         if (!proInstalled) {
            //             await checkInstall();
            //         } else {
            //             await checkInstall();
            //         }
            //     } else {
            //         await checkInstall();
            //     }
            // } else {
            //     console.error("Token not set in .npmrc file.");
            // }

            // skipcq: JS-0242
            const proKitInstalled = tryProKitInstall();
            if (!proKitInstalled) {
                // skipcq: JS-0242
                const proInstalled = tryFullProInstall();
                if (!proInstalled) {
                    await checkInstall();
                } else {
                    await checkInstall();
                }
            } else {
                await checkInstall();
            }

            //clean up the npmrc file
            await cleanup();

            //clear the flag
            process.env.FONTAWESOME_INSTALL_FAILED = false;

            return true;
        }
    } else {
        //clean up the npmrc file
        await cleanup();

        //clear the flag
        process.env.FONTAWESOME_INSTALL_FAILED = false;
        console.log(
            "Environment variable FONTAWESOME_PACKAGE_TOKEN not set. Skipping installation of Font Awesome Pro."
        );
    }
}

//Declare a function to remove the temporary npmrc file and restore the original if it exists.
async function cleanup() {
    //remove the temporary npmrc file
    try {
        try {
            await fs.access(".npmrc");
            // File exists, continue with the code
        } catch (error) {
            // File does not exist, handle the error
            console.log("File does not exist:", error);
            return false;
        }

        //remove the temporary npmrc file
        await fs.unlink(".npmrc");
    } catch (error) {
        // File does not exist, handle the error
        console.log("File does not exist or could not be accessed:", error);
    }

    //if there was a backup of the npmrc file, restore it.
    try {
        try {
            await fs.access(".npmrc.bak");
            // File exists, continue with the code
        } catch (error) {
            // File does not exist, handle the error
            console.log("Backup file does not exist:", error);
            return false;
        }

        //restore the backup of the npmrc file
        await fs.copyFile(".npmrc.bak", ".npmrc");

        //remove the backup of the npmrc file
        await fs.unlink(".npmrc.bak");
    } catch (error) {
        //if there was no backup of the npmrc file, do nothing.
        // File does not exist, handle the error
        console.log(
            "Backup file does not exist or could not be accessed:",
            error
        );
    }

    //return true if the cleanup was successful.
    return true;
}

//Declare a function to check if the installation failed and clean up the npmrc file.
async function checkInstall() {
    if (process.env.FONTAWESOME_INSTALL_FAILED) {
        console.error("Font Awesome Pro installation failed.");
        await cleanup();

        //clear the flag
        process.env.FONTAWESOME_INSTALL_FAILED = false;
    } else {
        console.log("Font Awesome Pro installation successful.");
        await cleanup();

        //clear the flag
        process.env.FONTAWESOME_INSTALL_FAILED = false;
    }
}

async function tryProKitInstall() {
    const installProcess = await exec(
        "npm install --no-save @awesome.me/kit-b72d7779ac@latest"
    );

    installProcess.stdout.on("data", (data) => {
        if (data) {
            const sanitizedData = data.replace(/\n|\r/g, "");
            console.log(`stdout: ${sanitizedData}`);
        }
    });

    installProcess.stderr.on("data", (data) => {
        if (data) {
            const sanitizedData = data.replace(/\n|\r/g, "");
            console.error(`stderr: ${sanitizedData}`);
        }
    });

    installProcess.on("close", (code) => {
        if (code === 0) {
            //clear the flag
            process.env.FONTAWESOME_INSTALL_FAILED = false;
            console.log(
                "@awesome.me/kit-b72d7779ac@latest installed successfully."
            );
        } else {
            //set a flag to indicate that the installation failed.
            process.env.FONTAWESOME_INSTALL_FAILED = true;
            console.error(
                `@awesome.me/kit-b72d7779ac@latest installation failed with exit code ${code}.`
            );

            //check to see if the error is because the package is already installed.
            if (code === 1) {
                console.log(
                    "@awesome.me/kit-b72d7779ac@latest is already installed. Skipping installation."
                );

                //clear the flag
                process.env.FONTAWESOME_INSTALL_FAILED = false;

                return true;
            }

            return false;
        }

        return true;
    });
}

async function tryFullProInstall() {
    if (process.env.FONTAWESOME_INSTALL_FAILED) {
        console.error("Font Awesome Pro Kit installation failed.");
        //clear the flag
        process.env.FONTAWESOME_INSTALL_FAILED = false;
        //if the package failed, but the token variable is set, try to install the regular pro package instead.
        const installProcess = await exec(
            "npm install --no-save @fortawesome/fontawesome-pro@latest"
        );

        installProcess.stdout.on("data", (data) => {
            if (data) {
                const sanitizedData = data.replace(/\n|\r/g, "");
                console.log(`stdout: ${sanitizedData}`);
            }
        });

        installProcess.stderr.on("data", (data) => {
            if (data) {
                const sanitizedData = data.replace(/\n|\r/g, "");
                console.error(`stderr: ${sanitizedData}`);
            }
        });

        installProcess.on("close", (code) => {
            if (code === 0) {
                //clear the flag
                process.env.FONTAWESOME_INSTALL_FAILED = false;
                console.log(
                    "@fortawesome/fontawesome-pro@latest installed successfully."
                );
            } else {
                //set a flag to indicate that the installation failed.
                process.env.FONTAWESOME_INSTALL_FAILED = true;
                console.error(
                    `@fortawesome/fontawesome-pro@latest installation failed with exit code ${code}.`
                );

                //check to see if the error is because the package is already installed.
                if (code === 1) {
                    console.log(
                        "@fortawesome/fontawesome-pro@latest is already installed. Skipping installation."
                    );

                    //clear the flag
                    process.env.FONTAWESOME_INSTALL_FAILED = false;

                    return true;
                }

                return false;
            }

            return true;
        });
    }
}

//Declare a function to set the token in the npmrc file.
async function setToken($token) {
    //get the contents of the npmrc file
    // skipcq: JS-0242
    let $npmrc = await fs.readFile(".npmrc", "utf8");

    //replace the token in the npmrc file
    $npmrc = $npmrc.replace("FONTAWESOME_PACKAGE_TOKEN", $token);

    //write the new contents to the npmrc file
    // skipcq: JS-0242
    const $writeFile = await fs.writeFile(".npmrc", $npmrc, "utf8");

    //check if the file was written successfully
    if ($writeFile) {
        console.log("Token set in .npmrc file.");

        //return true if the file was written successfully
        return true;
    } else {
        console.error("Token not set in .npmrc file.");

        //return false if the file was not written successfully
        return false;
    }
}
