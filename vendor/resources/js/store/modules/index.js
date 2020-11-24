import camelCase from "lodash/camelCase";

const requireModule = require.context(".", false, /\.js$/); //extract js files inside modules folder
const modules = {};

requireModule.keys().forEach(fileName => {
	// reject the index.js file or this file
	if (fileName === "./index.js") return;

  	// create the module name from file
  	const moduleName = camelCase(fileName.replace(/(\.\/|\.js)/g, ""));

  	// register file context under module name
  	modules[moduleName] = requireModule(fileName).default;

  	// override namespaced option
  	modules[moduleName].namespaced = true;
});

export default modules;