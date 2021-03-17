const fs = require('fs');
const path = require('path');

// Traverse for plugin dirs
function getPluginClientDirs(searchDir) {
    return fs.readdirSync(searchDir).reduce((acc, localDir) => {
        const pluginClientDir = path.join(searchDir, localDir, '/client');
        if (fs.existsSync(pluginClientDir)) {
            acc.push(pluginClientDir);
        }
        return acc;
    }, []);
}

// Find Entry points
function findEntryPoints(entryDir) {
    const subDirs = fs.readdirSync(entryDir);
    return subDirs instanceof Array ? subDirs.reduce((acc, localDir) => {
        const entry = path.join(entryDir, localDir, '/index.js');
        if (fs.existsSync(entry)) {
            acc[localDir] = { entry };
        }
        return acc;
    }, {}) : null;
}

const pages = getPluginClientDirs("../plugins/").map(entryDir => {
    return findEntryPoints(entryDir);
});

// Write to pages.json
fs.truncateSync('./pages.json', 0);
fs.writeFileSync('./pages.json', JSON.stringify(Object.assign(...pages), null, 4), 'utf-8');
