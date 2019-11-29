/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "/";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 1);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/gull/assets/js/script.js":
/*!********************************************!*\
  !*** ./resources/gull/assets/js/script.js ***!
  \********************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var $sidebarToggle = $(".menu-toggle");
var $sidebarLeft = $(".sidebar-left");
var $sidebarLeftSecondary = $(".sidebar-left-secondary");
var $sidebarOverlay = $(".sidebar-overlay");
var $mainContentWrap = $(".main-content-wrap");
var $sideNavItem = $(".nav-item");
var $searchInput = $(".search-bar input");
var $searchCloseBtn = $(".search-close");

function openSidebar() {
  $sidebarLeft.addClass("open");
  $mainContentWrap.addClass("sidenav-open");
}

function closeSidebar() {
  $sidebarLeft.removeClass("open");
  $mainContentWrap.removeClass("sidenav-open");
}

function openSidebarSecondary() {
  $sidebarLeftSecondary.addClass("open");
  $sidebarOverlay.addClass("open");
}

function closeSidebarSecondary() {
  $sidebarLeftSecondary.removeClass("open");
  $sidebarOverlay.removeClass("open");
}

function openSidebarOverlay() {
  $sidebarOverlay.addClass("open");
}

function closeSidebarOverlay() {
  $sidebarOverlay.removeClass("open");
}

function navItemToggleActive($activeItem) {
  var $navItem = $(".nav-item");
  $navItem.removeClass("active");
  $activeItem.addClass("active");
}

function isMobile() {
  return window && window.matchMedia("(max-width: 767px)").matches;
}

function initLayout() {
  // Makes secondary menu selected on page load
  $sideNavItem.each(function (index) {
    var $item = $(this);

    if ($item.hasClass("active")) {
      var dataItem = $item.data("item");
      $sidebarLeftSecondary.find("[data-parent=\"".concat(dataItem, "\"]")).show();
    }
  });

  if (isMobile()) {
    closeSidebar();
    closeSidebarSecondary();
  }
}

$(window).on("resize", function (event) {
  if (isMobile()) {
    closeSidebar();
    closeSidebarSecondary();
  } // console.log("desktop")

});
initLayout(); // Show Secondary menu area on hover on side menu item;

$sidebarLeft.find(".nav-item").on("mouseenter", function (event) {
  var $navItem = $(event.currentTarget);
  var dataItem = $navItem.data("item");

  if (dataItem) {
    navItemToggleActive($navItem);
    openSidebarSecondary();
  } else {
    closeSidebarSecondary();
  }

  $sidebarLeftSecondary.find(".childNav").hide();
  $sidebarLeftSecondary.find("[data-parent=\"".concat(dataItem, "\"]")).show();
}); // Hide secondary menu on click on overlay

$sidebarOverlay.on("click", function (event) {
  if (isMobile()) {
    closeSidebar();
  }

  closeSidebarSecondary();
}); // Toggle menus on click on header toggle icon

$sidebarToggle.on("click", function (event) {
  var isSidebarOpen = $sidebarLeft.hasClass("open");
  var isSidebarSecOpen = $sidebarLeftSecondary.hasClass("open");

  if (isSidebarOpen && isSidebarSecOpen && isMobile()) {
    closeSidebar();
    closeSidebarSecondary();
  } else if (isSidebarOpen && isSidebarSecOpen) {
    closeSidebarSecondary();
  } else if (isSidebarOpen) {
    closeSidebar();
  } else if (!isSidebarOpen && !isSidebarSecOpen) {
    openSidebar();
    openSidebarSecondary();
  }
}); // Search toggle

var $searchUI = $(".search-ui");
$searchInput.on("focus", function () {
  $searchUI.addClass("open");
});
$searchCloseBtn.on("click", function () {
  $searchUI.removeClass("open");
}); // Perfect scrollbar

$('.perfect-scrollbar, [data-perfect-scrollbar]').each(function (index) {
  var $el = $(this);
  var ps = new PerfectScrollbar(this, {
    suppressScrollX: $el.data('suppress-scroll-x'),
    suppressScrollY: $el.data('suppress-scroll-y')
  });
}); // Full screen

function cancelFullScreen(el) {
  var requestMethod = el.cancelFullScreen || el.webkitCancelFullScreen || el.mozCancelFullScreen || el.exitFullscreen;

  if (requestMethod) {
    // cancel full screen.
    requestMethod.call(el);
  } else if (typeof window.ActiveXObject !== "undefined") {
    // Older IE.
    var wscript = new ActiveXObject("WScript.Shell");

    if (wscript !== null) {
      wscript.SendKeys("{F11}");
    }
  }
}

function requestFullScreen(el) {
  // Supports most browsers and their versions.
  var requestMethod = el.requestFullScreen || el.webkitRequestFullScreen || el.mozRequestFullScreen || el.msRequestFullscreen;

  if (requestMethod) {
    // Native full screen.
    requestMethod.call(el);
  } else if (typeof window.ActiveXObject !== "undefined") {
    // Older IE.
    var wscript = new ActiveXObject("WScript.Shell");

    if (wscript !== null) {
      wscript.SendKeys("{F11}");
    }
  }

  return false;
}

function toggleFullscreen() {
  var elem = document.body;
  var isInFullScreen = document.fullScreenElement && document.fullScreenElement !== null || document.mozFullScreen || document.webkitIsFullScreen;

  if (isInFullScreen) {
    cancelFullScreen(document);
  } else {
    requestFullScreen(elem);
  }

  return false;
}

$('[data-fullscreen]').on('click', toggleFullscreen);

/***/ }),

/***/ 1:
/*!**************************************************!*\
  !*** multi ./resources/gull/assets/js/script.js ***!
  \**************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! C:\laragon\www\etax\resources\gull\assets\js\script.js */"./resources/gull/assets/js/script.js");


/***/ })

/******/ });