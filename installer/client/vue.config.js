// eslint-disable-next-line @typescript-eslint/no-var-requires
const DumpBuildTimestampPlugin = require('./scripts/plugins/DumpBuildTimestampPlugin');
const {defineConfig} = require('@vue/cli-service');

module.exports = defineConfig({
  css: {
    loaderOptions: {
      sass: {
        additionalData: `@import "@/styles/_variables.scss";`,
      },
    },
    extract: true,
  },
  transpileDependencies: true,
  configureWebpack: {
    resolve: {
      alias: {
        assets: '@ohrm/oxd/assets',
      },
    },
    plugins: [new DumpBuildTimestampPlugin()],
  },
  chainWebpack: (config) => {
    config.plugins.delete('html');
    config.plugins.delete('preload');
    config.plugins.delete('prefetch');
    config.plugins.delete('fork-ts-checker');
  },
  publicPath: '.',
  filenameHashing: false,
  runtimeCompiler: true,
});
