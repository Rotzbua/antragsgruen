define(["require","exports"],function(o,n){"use strict";var d=function(){function o(o){var n=o.find("button"),d=new Clipboard(n[0]);o.find(".clipboard-done").addClass("hidden"),d.on("success",function(d){o.find(".form-group").addClass("has-success has-feedback"),o.find(".clipboard-done").removeClass("hidden"),n.focus()}),d.on("error",function(){alert("Could not copy the URL to the clipboard")})}return o}();n.CopyUrlToClipboard=d});
//# sourceMappingURL=CopyUrlToClipboard.js.map