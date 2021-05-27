class DumpBuildTimestampPlugin {
  apply(compiler) {
    compiler.hooks.done.tap('Cache Invalidate Plugin', () => {
      /* eslint-disable @typescript-eslint/no-var-requires */
      const path = require('path');
      const fs = require('fs');

      const buildFile = path.join(__dirname, '/../../../../web/dist/build');
      const now = Date.now().toString();
      fs.writeFileSync(buildFile, now);
      console.info('Assets version: ', now);
      /* eslint-enable @typescript-eslint/no-var-requires */
    });
  }
}

module.exports = DumpBuildTimestampPlugin;
