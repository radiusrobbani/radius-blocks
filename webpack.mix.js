const mix = require("laravel-mix");
const fs = require("fs-extra");
const path = require("path");
const cliColor = require("cli-color");
const emojic = require("emojic");
const wpPot = require("wp-pot");
const min = Mix.inProduction() ? ".min" : "";
require("@tinypixelco/laravel-mix-wp-blocks");
// const IgnoreEmitPlugin = require("ignore-emit-webpack-plugin");

if (process.env.NODE_ENV === "package") {
	const package_path = path.resolve(__dirname);
	const package_slug = path.basename(path.resolve(package_path));

	mix.then(function () {
		const copyTo = path.resolve(`${package_slug}`);
		// Select All file then paste on list
		let includes = [
			"app",
			"assets",
			"languages",
			"vendor",
			"index.php",
			"README.md",
			"uninstall.php",
			`${package_slug}.php`,
		];
		fs.ensureDir(copyTo, function (err) {
			if (err) return console.error(err);
			includes.map((include) => {
				fs.copy(
					`${package_path}/${include}`,
					`${copyTo}/${include}`,
					function (err) {
						if (err) return console.error(err);
						console.log(
							cliColor.white(`=> ${emojic.smiley}  ${include} copied...`)
						);
					}
				);
			});
			console.log(
				cliColor.white(`=> ${emojic.whiteCheckMark}  Build directory created`)
			);
		});
	});

	return;
}
if (
	process.env.NODE_ENV === "development" ||
	process.env.NODE_ENV === "production"
) {
	if (Mix.inProduction()) {
		let languages = path.resolve("languages");
		fs.ensureDir(languages, function (err) {
			if (err) return console.error(err); // if file or folder does not exist
			wpPot({
				package: "Radius Block",
				bugReport: "",
				src: "app/**/*.php",
				domain: package_slug,
				destFile: `languages/${package_slug}.pot`,
			});
		});
	}

	if (!Mix.inProduction()) {
		// mix.sourceMaps();
	}
	mix.options({
		terser: {
			extractComments: false,
		},
		processCssUrls: false,
	});
	mix
		.block("src/blocks.js", "dist/blocks.build.js", {})
		.block("src/frontend.js", "dist/frontend.js");

	mix
		.js(`src/admin.js`, "assets/js/admin.js")
		.js(`src/deactivator.js`, "assets/js/deactivator.build.js")
		.sass("src/scss/admin.scss", "assets/css/admin.css");

}
// module.exports = {
// 	plugins: [
// 		new IgnoreEmitPlugin(["blocks.build.asset.php", "blocks.build.js.map"]),
// 	],
// };
