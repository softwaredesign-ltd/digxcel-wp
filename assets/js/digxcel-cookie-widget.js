
const cookieEntry = "<id>=<value>; expires=<expires>; domain=<domain>;";
let iframeSource = "http://app.<orgId>.digxcel.com:4200/cookie-consent?dpsId=<dpsId>";
const iframeId = "digxcel-modal";
const bannerId = "digxcel-banner";
const overlayId = "digxcel-overlay";
const bannerDismissedCookie = "digxcel-banner-dismissed";
let orgId = null;
let dpsId = null;
let modalReady = false;

window.onload = function() {
  var widgetKey = document.getElementById("digxcelConfig").getAttribute("key").split("-");
  orgId = widgetKey[0];
  dpsId = widgetKey[1];

  if(getCookie(bannerDismissedCookie) === "true")
    return null;

  var iframe = document.createElement('iframe');
  iframe.id = iframeId;
  iframe.src = iframeSource.replace("<orgId>", orgId).replace("<dpsId>", dpsId);
  iframe.style = "z-index: 1001; position: fixed; top: -100%; opacity: 0; max-height: 550px;";
  document.body.appendChild(iframe);

  var overlay = document.createElement('div');
  overlay.id = overlayId;
  overlay.style = "z-index: 1000; display: none; position: fixed; top: 0; right: 0; bottom: 0; left: 0; background-color: #000; opacity: 0.3;";
  overlay.onclick = toggleIframe;
  document.body.appendChild(overlay);

  var banner = document.createElement('div');
  banner.innerHTML = `
    <div style="position: fixed; bottom: 0px; left: 0px; right: 0px; height: 120px; background-color: rgb(0, 0, 0); opacity: 0.8;"></div>
    <div style="position: fixed; bottom: 40px; right: 50px; color: rgb(221, 221, 221);">
      <span><h3>This site uses Cookies to improve your experience</h3></span>
      <span style="float: right;">
        <a style="margin-right: 10px;" href="#" onclick="toggleIframe()">Cookie Settings</a>
        <a style="margin-right: 10px;" href="#" onclick="toggleBanner()">Dismiss</a>
      </span>
    </div>
  `;
  banner.style = "display: block;";
  banner.id = bannerId;
  document.body.appendChild(banner);
}

function toggleIframe() {
  if( !modalReady )
    return null;
  if( document.getElementById(iframeId).style.opacity == '1' ) {
    document.getElementById(iframeId).style.opacity = '0';
    document.getElementById(iframeId).style.top = '-100%';
  } else {
    document.getElementById(iframeId).style.opacity = '1';
    document.getElementById(iframeId).style.top = '20%';
  }
  toggleById(overlayId);
}

function toggleBanner() {
  toggleById(bannerId);
  storeCookie(bannerDismissedCookie, "true", "Sat, 01-Jan-2050 00:00:00 GMT", "." + window.location.hostname);
}

function resizeIframe(height, width) {
  modalReady = true;
  let iframe = document.getElementById(iframeId);
  iframe.style.width = width + 'px';
  iframe.style.height = height + 'px';
  centreIframe();
}

function centreIframe() {
  let iframe = document.getElementById(iframeId);
  document.getElementById(iframeId).style.left = ( window.innerWidth - parseInt(iframe.style.width) ) / 2  + 'px';
}

function toggleById(elementId) {
  let elementDisplay = document.getElementById(elementId).style.display;
  document.getElementById(elementId).style.display = elementDisplay == 'block' ? 'none' : 'block';
}

function blockCookie(cookieName) {
  storeCookie(cookieName, "expired", "Sat, 01-Jan-2000 00:00:00 GMT", "." + window.location.hostname);
}

function getCookie(cookieName) {
  var value = "; " + document.cookie;
  var parts = value.split("; " + cookieName + "=");
  if (parts.length == 2) return parts.pop().split(";").shift();
}

function storeCookie(id, value, expires, domain) {
  document.cookie = cookieEntry.replace(
    "<id>", id
  ).replace(
    "<value>", value
  ).replace(
    "<expires>", expires
  ).replace(
    "<domain>", domain
  );
}

function syncConsents(consentCookieId, consents) {
  if( consentCookieId.indexOf(dpsId) != -1 ){
    storeCookie(
      "digxcel-consents",
      consents,
      "Sat, 01-Jan-2050 00:00:00 GMT",
      window.location.hostname
    );
  }
}

window.onresize = function(event) {
  centreIframe();
};

window.addEventListener("message", function(event) {
  switch (event.data.action) {
    case "block":
      blockCookie(event.data.data);
      break;
    case "sync":
      syncConsents(event.data.data.consentCookieId, event.data.data.consents);
      break;
    case "hideIframe":
      toggleIframe();
      break;
    case "resizeIframe":
      resizeIframe(event.data.data.height, event.data.data.width);
      break;
  }
}, false);
