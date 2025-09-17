const rootElement = document.documentElement;
const darkModeClassName = "dark";
const darkModeStorageKey = "user-color-scheme";
const validColorModeKeys = { dark: true, light: true };
const invertDarkModeObj = { dark: "light", light: "dark" };

// localStorage 操作封装
const storage = {
	get: (key) => {
	try {
		return localStorage.getItem(key);
	} catch (e) {
		console.error("Storage Get Error:", e);
		return null;
	}
	},
	set: (key, value) => {
	try {
		localStorage.setItem(key, value);
		return true;
	} catch (e) {
		console.error("Storage Set Error:", e);
		return false;
	}
	},
	remove: (key) => {
	try {
		localStorage.removeItem(key);
		return true;
	} catch (e) {
		console.error("Storage Remove Error:", e);
		return false;
	}
	},
};

// 获取系统偏好色彩方案
const getSystemColorScheme = () =>
	window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";

// 应用主题：先清除，再添加
const applyColorScheme = (mode) => {
	rootElement.classList.remove(...Object.keys(validColorModeKeys));
	if (validColorModeKeys[mode]) {
	rootElement.classList.add(mode);
	}
};

// 重置为系统默认主题并移除本地存储
const resetColorScheme = () => {
	rootElement.classList.remove(...Object.keys(validColorModeKeys));
	storage.remove(darkModeStorageKey);
	applyColorScheme(getSystemColorScheme());
};

// 初始化主题逻辑：首次访问或加载时执行
const initializeColorScheme = (mode = null) => {
	const savedSetting = mode ?? storage.get(darkModeStorageKey);
	const systemScheme = getSystemColorScheme();

	if (savedSetting === systemScheme) {
	// 用户自定义设置与系统一致 → 视为不需要记住，回到系统控制
	resetColorScheme();
	} else if (validColorModeKeys[savedSetting]) {
	applyColorScheme(savedSetting);
	} else {
	// 无效或无设置 → 跟随系统
	resetColorScheme();
	}
};

// 切换主题：手动按钮调用
const toggleColorScheme = () => {
	const savedSetting = storage.get(darkModeStorageKey);
	const currentMode = validColorModeKeys[savedSetting]
		? savedSetting
		: getSystemColorScheme();
	const newMode = invertDarkModeObj[currentMode];

	storage.set(darkModeStorageKey, newMode);
	applyColorScheme(newMode);

	return newMode;
};

// 系统主题变更监听
const handleSystemSchemeChange = (event) => {
	const newScheme = event.matches ? "dark" : "light";
	resetColorScheme();
	applyColorScheme(newScheme);
};

// 初始化主题
initializeColorScheme();

// 监听系统主题变化
const mediaQuery = window.matchMedia("(prefers-color-scheme: dark)");
if (typeof mediaQuery.addEventListener === "function") {
	mediaQuery.addEventListener("change", handleSystemSchemeChange);
} else if (typeof mediaQuery.addListener === "function") {
	mediaQuery.addListener(handleSystemSchemeChange); // 兼容旧浏览器
}