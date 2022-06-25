module.exports = {
	"extends": "eslint:recommended",
	"env": {
		"browser": true,
		"es2021": true
	},
	"ignorePatterns": [
		"admin/js/min/*.min.js",
		"admin/js/tipso.js"
	],
	"globals": {
		"lityScriptData": true,
		"jQuery": true
	},
	"parserOptions": {
		"ecmaVersion": "latest"
	},
	"rules": {
		"no-extra-boolean-cast": "off"
	}
}
