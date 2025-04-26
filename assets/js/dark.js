const rootElement = document.documentElement;
const darkModeClassName = "dark";
const darkModeStorageKey = "user-color-scheme";
const validColorModeKeys = { dark: true, light: true };
const invertDarkModeObj = { dark: "light", light: "dark" };

const setLocalStorage = (key, value) => {
	try {
		localStorage.setItem(key, value);
	} catch (e) {}
};

const removeLocalStorage = (key) => {
	try {
		localStorage.removeItem(key);
	} catch (e) {}
};

const getLocalStorage = (key) => {
	try {
		return localStorage.getItem(key);
	} catch (e) {
		return null;
	}
};

const getModeFromCSSMediaQuery = () => {
	return window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
};

const setColorScheme = (mode) => {
	rootElement.classList.remove(mode, invertDarkModeObj[mode]);
	rootElement.classList.add(mode);
};

const resetRootDarkModeClassAndLocalStorage = () => {
	rootElement.classList.remove(darkModeClassName, invertDarkModeObj[darkModeClassName]);
	removeLocalStorage(darkModeStorageKey);
};

const applyCustomDarkModeSettings = (mode) => {
	// 接受从「开关」处传来的模式，或者从 localStorage 读取
	const currentSetting = mode || getLocalStorage(darkModeStorageKey);
	if (currentSetting === getModeFromCSSMediaQuery()) {
		// 当用户自定义的显示模式和 prefers-color-scheme 相同时重置、恢复到自动模式
		resetRootDarkModeClassAndLocalStorage();
		setColorScheme(currentSetting);
	} else if (validColorModeKeys[currentSetting]) {
		rootElement.classList.add(currentSetting);
		rootElement.classList.remove(invertDarkModeObj[currentSetting]);
	} else {
		// 首次访问或从未使用过开关、localStorage 中没有存储的值，currentSetting 是 null
		// 或者 localStorage 被篡改，currentSetting 不是合法值
		resetRootDarkModeClassAndLocalStorage();
		// 使用系统当前方案
		setColorScheme(getModeFromCSSMediaQuery());
	}
};

const toggleCustomDarkMode = () => {
	let currentSetting = getLocalStorage(darkModeStorageKey);
	if (validColorModeKeys[currentSetting]) {
		// 从 localStorage 中读取模式，并取相反的模式
		currentSetting = invertDarkModeObj[currentSetting];
	} else if (currentSetting === null) {
		// localStorage 中没有相关值，或者 localStorage 抛了 Error
		// 从 CSS 中读取当前 prefers-color-scheme 并取相反的模式
		currentSetting = invertDarkModeObj[getModeFromCSSMediaQuery()];
	} else {
		// 不知道出了什么幺蛾子，比如 localStorage 被篡改成非法值
		return; // 直接 return;
	}
	// 将相反的模式写入 localStorage
	setLocalStorage(darkModeStorageKey, currentSetting);

	return currentSetting;
};

// 当页面加载时，将显示模式设置为 localStorage 中自定义的值（如果有的话）
applyCustomDarkModeSettings();

const onSystemSchemeChanged = (event) => {
	// 获取新的系统主题方案
	const newColorScheme = event.matches ? "dark" : "light";
	// 用户主动配置了系统方案，清除用户之前记忆
	resetRootDarkModeClassAndLocalStorage();
	// 使用系统当前方案
	setColorScheme(newColorScheme);
};

const darkModePreference = window.matchMedia("(prefers-color-scheme: dark)");

// recommended method for newer browsers: specify event-type as first argument
darkModePreference.addEventListener("change", onSystemSchemeChanged);

// deprecated method for backward compatibility
darkModePreference.addListener(onSystemSchemeChanged);