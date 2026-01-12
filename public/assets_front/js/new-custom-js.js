/*
== malihu jquery custom scrollbar plugin == 
Version: 3.1.5 
Plugin URI: http://manos.malihu.gr/jquery-custom-content-scroller 
Author: malihu
Author URI: http://manos.malihu.gr
License: MIT License (MIT)
*/

/*
Copyright Manos Malihutsakis (email: manos@malihu.gr)

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
*/

/*
The code below is fairly long, fully commented and should be normally used in development. 
For production, use either the minified jquery.mCustomScrollbar.min.js script or 
the production-ready jquery.mCustomScrollbar.concat.min.js which contains the plugin 
and dependencies (minified). 
*/

// input selection section reveal
document.addEventListener("DOMContentLoaded", () => {
  const radioGroups = {};

  // Group radios by name
  document.querySelectorAll('input[type="radio"]').forEach((radio) => {
    const name = radio.name;
    if (!radioGroups[name]) radioGroups[name] = [];
    radioGroups[name].push(radio);
  });

  // Show/hide targets + toggle "required" for radio buttons
  function handleRadioChange(selectedRadio, group) {
    group.forEach((r) => {
      const targetId = r.getAttribute("data-toggle-target");
      const targetEl = targetId ? document.getElementById(targetId) : null;

      if (targetEl) {
        const inputs = targetEl.querySelectorAll("input, select, textarea");

        if (r.checked) {
          targetEl.style.display = "block";
          inputs.forEach((input) => (input.required = true));
        } else {
          targetEl.style.display = "none";
          inputs.forEach((input) => (input.required = false));
        }
      }
    });
  }

  // Bind listeners + apply initial state for radios
  Object.values(radioGroups).forEach((group) => {
    group.forEach((radio) => {
      radio.addEventListener("change", () => handleRadioChange(radio, group));
      if (radio.checked) handleRadioChange(radio, group); // Run once on load
    });
  });

  // Handle checkboxes separately
  document
    .querySelectorAll('input[type="checkbox"][data-toggle-target]')
    .forEach((checkbox) => {
      const targetId = checkbox.getAttribute("data-toggle-target");
      const targetEl = targetId ? document.getElementById(targetId) : null;

      function handleCheckboxChange() {
        if (targetEl) {
          const inputs = targetEl.querySelectorAll("input, select, textarea");
          if (checkbox.checked) {
            targetEl.style.display = "block";
            inputs.forEach((input) => (input.required = true));
          } else {
            targetEl.style.display = "none";
            inputs.forEach((input) => (input.required = false));
          }
        }
      }

      checkbox.addEventListener("change", handleCheckboxChange);
      handleCheckboxChange(); // Run once on load
    });

  // Handle anchor tags with data-toggle-target
  document.querySelectorAll("a[data-toggle-target]").forEach((anchor) => {
    const targetId = anchor.getAttribute("data-toggle-target");
    const targetEl = targetId ? document.getElementById(targetId) : null;

    anchor.addEventListener("click", (e) => {
      e.preventDefault();

      if (targetEl) {
        const inputs = targetEl.querySelectorAll("input, select, textarea");
        const isVisible = targetEl.style.display === "block";

        if (isVisible) {
          targetEl.style.display = "none";
          inputs.forEach((input) => (input.required = false));
        } else {
          targetEl.style.display = "block";
          inputs.forEach((input) => (input.required = true));
        }
      }
    });
  });
});
