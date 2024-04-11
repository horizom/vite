import { Plugin, ResolvedConfig, UserConfig, loadEnv } from "vite";
import colors from "picocolors";

interface PluginConfig {
  /**
   * The path or paths of the entry points to compile.
   */
  input: string | string[];

  /**
   * Laravel's public directory.
   *
   * @default 'public'
   */
  publicDirectory?: string;

  /**
   * The public subdirectory where compiled assets should be written.
   *
   * @default 'build'
   */
  buildDirectory?: string;
}

function horizom(pluginConfig: PluginConfig): Plugin {
  let resolvedConfig: ResolvedConfig;
  let userConfig: UserConfig;

  return {
    name: "vite-plugin-horizom",
    apply: "serve",
    config: (config, { command, mode }) => {
      userConfig = config;

      return {
        publicDir: userConfig.publicDir ?? false,
        build: {
          rollupOptions: {
            input: pluginConfig.input,
          },
          assetsInlineLimit: userConfig.build?.assetsInlineLimit ?? 0,
        },
      };
    },
    configResolved(config) {
      resolvedConfig = config;
    },
    configureServer(server) {
      const envDir = resolvedConfig.envDir || process.cwd();
      const appUrl =
        loadEnv(resolvedConfig.mode, envDir, "APP_URL").APP_URL ?? "undefined";

      const parts = appUrl.split(":");
      const port = parts.length > 2 ? `:${parts[2]}` : "";
      const url = parts[0] + ":" + parts[1];
      const version = "1.0.0";

      server.httpServer?.once("listening", () => {
        setTimeout(() => {
          server.config.logger.info(
            `\n  ${colors.blue(`${colors.bold("PHP")}`)} ${colors.blue(
              "v8.3.1"
            )}   ${colors.dim("plugin")} ${colors.white(`${colors.bold(`v${version}`)}`)}`
          );
          server.config.logger.info("");
          server.config.logger.info(
            `  ${colors.green("➜")}  ${colors.white(
              `${colors.bold("APP_URL:")}`
            )} ${colors.cyan(`${url}${colors.bold(port)}`)}`
          );
          server.config.logger.info("");
        }, 100);
      });
    },
  };
}

export default horizom;
