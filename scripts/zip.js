const fs = require('fs');
const archiver = require('archiver');
const ignore = require('ignore');
const path = require('path');

// Calculate plugin directory path relative to this script's location
const pluginPath = path.resolve(__dirname, '..'); // Goes up to the plugin folder from scripts folder
const outputPath = path.join(pluginPath, 'plugin-release.zip'); // Output in the plugin directory

// Read .gitignore
const gitignorePath = path.join(pluginPath, '.gitignore');
if (!fs.existsSync(gitignorePath)) {
    console.error('.gitignore file not found.');
    process.exit(1);
}
const gitignore = fs.readFileSync(gitignorePath, 'utf8');
const ig = ignore().add(gitignore);

// Create a file to stream archive data to.
const output = fs.createWriteStream(outputPath);
const archive = archiver('zip', {
    zlib: { level: 9 } // Sets the compression level.
});

// Listen for all archive data to be written
output.on('close', function() {
    console.log(`Archive completed. Total bytes: ${archive.pointer()}`);
});

archive.on('warning', function(err) {
    if (err.code === 'ENOENT') {
        // Log warning
        console.warn(err);
    } else {
        // Throw error
        throw err;
    }
});

archive.on('error', function(err) {
    throw err;
});

// Pipe archive data to the file
archive.pipe(output);

// Add the files and folders recursively
const addFilesRecursively = (dir, prefix = '') => {
    fs.readdirSync(dir).forEach((file) => {
        const filePath = path.join(dir, file);
        if (fs.statSync(filePath).isDirectory()) {
            // Continue the recursion if it's not the scripts folder itself to avoid zipping the script
            if (path.relative(pluginPath, filePath) !== 'scripts') {
                addFilesRecursively(filePath, `${prefix}${file}/`);
            }
        } else {
            if (!ig.ignores(`${prefix}${file}`)) { // Check against .gitignore
                archive.file(filePath, { name: `${prefix}${file}` });
            }
        }
    });
};

// Start the archiving process
addFilesRecursively(pluginPath);
archive.finalize();
