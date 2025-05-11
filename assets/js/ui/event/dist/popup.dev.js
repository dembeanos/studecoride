"use strict";

window.addEventListener('load', function () {
  // Fonction de gestion de la réponse
  window.handleResponse = function (data) {
    console.log("Réponse reçue:", data);

    if (typeof data === "string") {
      try {
        data = JSON.parse(data);
      } catch (e) {
        console.error("Réponse invalide", data);
        return;
      }
    }

    switch (data.type) {
      case "user_error":
        showMessage(data.message, data.target, "error");
        break;

      case "user_success":
        showMessage(data.message, data.target, "success");
        break;

      case "dev":
        console.log(data.message);
        break;

      case "popup":
        showPopup(data.message);
        break;

      default:
        console.warn("Type inconnu", data);
    }
  }; // Affichage du message de succès ou d'erreur


  function showMessage(message, targetId, type) {
    var target = document.getElementById(targetId);
    if (!target) return;
    var existing = target.parentElement.querySelector('.field-message');
    if (existing) existing.remove();
    var msg = document.createElement("p");
    msg.classList.add("field-message", type === "error" ? "error-message" : "success-message");
    msg.textContent = message;
    msg.style.marginBottom = "4px";
    target.parentElement.insertBefore(msg, target); // Supprime le message après 5 secondes

    setTimeout(function () {
      if (msg.parentElement) {
        msg.parentElement.removeChild(msg);
      }
    }, 5000);
  } // Affichage d'une popup


  function showPopup(message) {
    var existingPopup = document.querySelector(".popup-overlay");
    if (existingPopup) existingPopup.remove();
    var overlay = document.createElement("div");
    overlay.classList.add("popup-overlay");
    var popup = document.createElement("div");
    popup.classList.add("popup-box");
    var closeBtn = document.createElement("span");
    closeBtn.innerHTML = "&times;";
    closeBtn.classList.add("popup-close");
    closeBtn.addEventListener("click", function () {
      return overlay.remove();
    });
    var msg = document.createElement("p");
    msg.textContent = message;
    var okBtn = document.createElement("button");
    okBtn.textContent = "OK";
    okBtn.classList.add("popup-ok");
    okBtn.addEventListener("click", function () {
      return overlay.remove();
    });
    popup.appendChild(closeBtn);
    popup.appendChild(msg);
    popup.appendChild(okBtn);
    overlay.appendChild(popup);
    document.body.appendChild(overlay);
    setTimeout(function () {
      if (overlay.parentElement) {
        overlay.remove();
      }
    }, 5000);
  } // Fonction pour afficher un popup interactif


  window.sendInteractivePopup = function (content) {
    var fields = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : [];
    var existingPopup = document.querySelector(".popup-overlay");
    if (existingPopup) existingPopup.remove();
    var overlay = document.createElement("div");
    overlay.classList.add("popup-overlay");
    var popup = document.createElement("div");
    popup.classList.add("popup-box");
    var closeBtn = document.createElement("span");
    closeBtn.innerHTML = "&times;";
    closeBtn.classList.add("popup-close");
    closeBtn.addEventListener("click", function () {
      return overlay.remove();
    });
    var contentDiv = document.createElement("div");
    contentDiv.classList.add("popup-content");
    contentDiv.innerHTML = content;
    popup.appendChild(closeBtn);
    popup.appendChild(contentDiv);

    if (fields.length > 0) {
      fields.forEach(function (field) {
        var fieldDiv = document.createElement("div");
        fieldDiv.classList.add("popup-field");
        var label = document.createElement("label");
        label.textContent = field.label;
        fieldDiv.appendChild(label);
        var input;

        switch (field.type) {
          case 'textarea':
            input = document.createElement("textarea");
            break;

          case 'select':
            input = document.createElement("select");

            if (field.options) {
              field.options.forEach(function (option) {
                var optionElem = document.createElement("option");
                optionElem.value = option;
                optionElem.textContent = option;
                input.appendChild(optionElem);
              });
            }

            break;

          default:
            input = document.createElement("input");
            input.type = field.type || "text";
        }

        input.name = field.name;
        fieldDiv.appendChild(input);
        popup.appendChild(fieldDiv);
      });
    }

    var okBtn = document.createElement("button");
    okBtn.textContent = "OK";
    okBtn.classList.add("popup-ok");
    okBtn.addEventListener("click", function () {
      return overlay.remove();
    });
    popup.appendChild(okBtn);
    overlay.appendChild(popup);
    document.body.appendChild(overlay);
  };
});