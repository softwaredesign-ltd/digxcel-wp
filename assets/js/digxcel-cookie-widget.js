
const dxIframeId = "digxcel-modal";
const dxBannerId = "digxcel-banner";
const dxOverlayId = "digxcel-overlay";
const dxBannerDismissedCookie = "digxcel-banner-dismissed";
let dpsId = null;
let dxModalReady = false;

window.onload = function() {
  const iframeSource = document.getElementById("digxcelConfig").getAttribute("url");
  dpsId = iframeSource.split("?dpsId=")[1];

  var iframe = document.createElement('iframe');
  iframe.id = dxIframeId;
  iframe.src = iframeSource;
  iframe.style = "z-index: 1001; position: fixed; top: -100%; opacity: 0; max-height: 550px;";
  document.body.appendChild(iframe);

  var overlay = document.createElement('div');
  overlay.id = dxOverlayId;
  overlay.style = "z-index: 1000; display: none; position: fixed; top: 0; right: 0; bottom: 0; left: 0; background-color: #000; opacity: 0.3;";
  overlay.onclick = dxToggleIframe;
  document.body.appendChild(overlay);

  if(dxGetCookie(dxBannerDismissedCookie) === "true")
    return null;

  var banner = document.createElement('div');
  banner.innerHTML = `
    <div style="position: fixed; bottom: 0px; left: 0px; right: 0px; height: 100px; background-color: rgb(0, 0, 0); opacity: 0.8;"></div>
    <div style="position: fixed; bottom: 16px; right: 50px; color: rgb(221, 221, 221);">
      <span><h4 style="color: #ffffff; margin-bottom: 13px; margin-right: 9px;">This site uses Cookies to improve your experience</h4></span>
      <span style="float: right;">
        <a style="margin-right: 10px;" href="#" onclick="dxToggleIframe()">Cookie Settings</a>
        <a style="margin-right: 10px;" href="#" onclick="dxToggleBanner()">Dismiss</a>
      </span>
    </div>
  `;
  banner.style = "display: block;";
  banner.id = dxBannerId;
  document.body.appendChild(banner);
}

function dxToggleIframe() {
  if( !dxModalReady )
    return null;
  if( document.getElementById(dxIframeId).style.opacity == '1' ) {
    document.getElementById(dxIframeId).style.opacity = '0';
    document.getElementById(dxIframeId).style.top = '-100%';
  } else {
    document.getElementById(dxIframeId).style.opacity = '1';
    document.getElementById(dxIframeId).style.top = '20%';
  }
  dxToggleById(dxOverlayId);
}

function dxToggleBanner() {
  dxToggleById(dxBannerId);
  dxStoreCookie(dxBannerDismissedCookie, "true", "Sat, 01-Jan-2050 00:00:00 GMT", "." + window.location.hostname, '/');
}

function dxResizeIframe(height, width) {
  dxModalReady = true;
  let iframe = document.getElementById(dxIframeId);
  iframe.style.width = width + 'px';
  iframe.style.height = height + 'px';
  dxCentreIframe();
}

function dxCentreIframe() {
  let iframe = document.getElementById(dxIframeId);
  if(iframe)
    document.getElementById(dxIframeId).style.left = ( window.innerWidth - parseInt(iframe.style.width) ) / 2  + 'px';
}

function dxToggleById(elementId) {
  let element = document.getElementById(elementId);
  if(element != null)
    document.getElementById(elementId).style.display = element.style.display == 'block' ? 'none' : 'block';
}

function dxBlockCookie(cookie) {
  dxExpireCookie(cookie.name, "expired", "Sat, 01-Jan-2000 00:00:00 GMT", cookie.domain, cookie.path);
  dxExpireCookie(cookie.name, "expired", "Sat, 01-Jan-2000 00:00:00 GMT", cookie.domain, '.' + cookie.path);
  dxExpireCookie(cookie.name, "expired", "Sat, 01-Jan-2000 00:00:00 GMT", cookie.domain, '/' + window.location.pathname.split('/').join(''));
}

function dxGetCookie(cookieName) {
  var value = "; " + document.cookie;
  var parts = value.split("; " + cookieName + "=");
  if (parts.length == 2) return parts.pop().split(";").shift();
}

function dxExpireCookie(id, value, expires, domain, path) {
  document.cookie = `${id}=${value}; expires=${expires}; domain=${domain}; path=${path}`;
  document.cookie = `${id}=${value}; expires=${expires}; domain=${domain};`;
  document.cookie = `${id}=${value}; expires=${expires};`;
}

function dxStoreCookie(id, value, expires, domain, path) {
  if( domain && path ){
    document.cookie = `${id}=${value}; expires=${expires}; domain=${domain}; path=${path}`;
  } else if ( domain ) {
    document.cookie = `${id}=${value}; expires=${expires}; domain=${domain};`;
  } else {
    document.cookie = `${id}=${value}; expires=${expires};`;
  }
}

function dxSyncConsents(consentCookieId, consents) {
  if( consentCookieId.indexOf(dpsId) != -1 ){
    dxStoreCookie("digxcel-consents", consents, "Sat, 01-Jan-2050 00:00:00 GMT", window.location.hostname, "/");
  }
}

window.onresize = function(event) {
  dxCentreIframe();
};

window.addEventListener("message", function(event) {
  switch (event.data.action) {
    case "block":
      dxBlockCookie(event.data.data);
      break;
    case "sync":
      dxSyncConsents(event.data.data.consentCookieId, event.data.data.consents);
      break;
    case "hideIframe":
      dxToggleIframe();
      break;
    case "resizeIframe":
      dxResizeIframe(event.data.data.height, event.data.data.width);
      break;
  }
}, false);
