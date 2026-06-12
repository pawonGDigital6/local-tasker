const defaults = require("@wordpress/scripts/config/webpack.config");
const CopyWebpackPlugin = require("copy-webpack-plugin");
const path = require("path");

const copyPreviewImage = [];

if (process.env.COPY_PREVIEW_IMAGE === "true") {
    copyPreviewImage.push(
        new CopyWebpackPlugin({
            patterns: [
                {
                    from: "**/preview.png",
                    to: "[path][name][ext]",
                    context: path.resolve(__dirname, "src", "blocks")
                },
            ],
        }),
    )
}

module.exports = {
    ...defaults,
    output: {
        ...defaults.output,
        // Only wipe the output folder on a real production build.
        // In watch mode (start / start:global) wp-scripts re-runs this
        // "clean" step on every save, which on Windows tries to delete
        // files (like editor.asset.php) while WAMP/PHP still has them
        // open -> EBUSY: resource busy or locked.
        clean: defaults.mode === "production",
    },
    watchOptions: {
        ...defaults.watchOptions,
        // Make sure webpack never treats its own output (or its
        // filesystem cache) as a "source" change. Without this, on
        // Windows the write -> watch-event -> rebuild -> write cycle
        // can loop forever and eventually collide with a file lock
        // (EBUSY) on build/global/editor.asset.php etc.
        ignored: [
            ...(defaults.watchOptions?.ignored || []),
            "**/build/**",
            "**/node_modules/**",
        ],
    },
    externals: {
        ...defaults.externals,
        jquery: "jQuery",
    },
    module: {
        ...defaults.module,
        rules: [
            ...defaults.module.rules,
            {
                test: /\.css$/,
                use: [
                    {
                        loader: 'postcss-loader',
                        options: {
                            postcssOptions: {
                                plugins: [
                                    require('@tailwindcss/postcss'),
                                ],
                            },
                        },
                    },
                ],
            },
        ],
    },
    plugins: [
        ...defaults.plugins,
        ...copyPreviewImage
    ],
};