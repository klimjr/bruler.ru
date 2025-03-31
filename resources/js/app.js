import.meta.glob(["../images/**/*"]);
import Swiper from "swiper/bundle";
import 'livewire-sortable'

import "swiper/css/bundle";

import axios from "axios";
import "./bootstrap";
import "./video";
import "./ResizeSensor.js";
import "./StickySidebar.js";

import * as noUiSlider from "nouislider";
import "nouislider/dist/nouislider.css";

import PinchZoom from "pinch-zoom-js";

window.axios = axios;
window.Swiper = Swiper;
window.noUiSlider = noUiSlider;
window.PinchZoom = PinchZoom;

document.addEventListener("DOMContentLoaded", function () {
  const eventCallback = (e) => {
    let el = e.target,
      pattern = el.dataset.phonePattern || "+7(___) ___-__-__",
      matrix = pattern,
      i = 0,
      def = matrix.replace(/\D/g, ""), // Только цифры из шаблона
      val = el.value.replace(/\D/g, ""); // Только цифры из текущего значения

    // Если поле пустое, добавляем "+7"
    if (!el.value.startsWith("+")) {
      el.value = "+7";
      return;
    }

    // Предотвращаем удаление "+"
    if (e.inputType === "deleteContentBackward" && el.selectionStart === 1) {
      e.preventDefault();
      return;
    }

    // Оставляем только пользовательскую цифру после "+"
    if (val.length > 0 && val.charAt(0) !== "7") {
      def = def.slice(1); // Удаляем "7" из дефолта, чтобы не навязывать её
    }

    // Восстанавливаем формат, но учитываем первую цифру пользователя
    el.value = matrix.replace(/./g, (a) =>
      /[_\d]/.test(a) && i < val.length
        ? val.charAt(i++)
        : i >= val.length
        ? ""
        : a
    );
  };

  const onlyWords = (e) => {
    e.target.value = e.target.value.replace(/[^a-zA-Zа-яА-Я]/g, "");
  };

  document
    .querySelectorAll("[data-phone-pattern]")
    .forEach((el) =>
      ["input", "blur", "focus"].forEach((ev) =>
        el.addEventListener(ev, eventCallback)
      )
    );

  document.querySelectorAll("[data-text-pattern]").forEach((el) => {
    el.value = el.value.replace(/[^a-zA-Zа-яА-Я]/g, "");
    return ["input", "blur", "focus"].forEach((ev) =>
      el.addEventListener(ev, onlyWords)
    );
  });

    document
        .querySelectorAll("[data-pattern-english-number]")
        .forEach((el) =>
            ["input", "blur", "focus"].forEach((ev) =>
                el.addEventListener(ev, (e) => {
                    e.target.value = onlyEnglishAndNumbers(e.target.value);
                })
            )
        );

    window.addEventListener('scroll', function() {
        const header = document.querySelector('.js-header');
        if (window.scrollY > 100) {
            header.classList.add('shadow');
        } else {
            header.classList.remove('shadow');
        }
    });
});


function onlyEnglishAndNumbers(input) {
    // Удаляем все символы, кроме английских букв и цифр
    const cleanedInput = input.replace(/[^a-zA-Z0-9]/g, '');
    return cleanedInput;
}
