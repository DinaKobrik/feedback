document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("feedback-form");
  const nameInput = document.getElementById("feedback_name");
  const emailInput = document.getElementById("feedback_email");
  const messageInput = document.getElementById("feedback_message");
  const nameError = document.getElementById("feedback_name_error");
  const emailError = document.getElementById("feedback_email_error");
  const messageError = document.getElementById("feedback_message_error");

  form.addEventListener("submit", function (event) {
    let hasErrors = false;

    // Сброс ошибок
    nameError.textContent = "";
    emailError.textContent = "";
    messageError.textContent = "";

    // Валидация имени
    if (!nameInput.value.trim()) {
      nameError.textContent = "Имя не может быть пустым";
      hasErrors = true;
    }

    // Валидация email
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(emailInput.value)) {
      emailError.textContent = "Некорректный email";
      hasErrors = true;
    }

    // Валидация сообщения
    const message = messageInput.value;
    const strippedMessage = message.replace(/<[^>]+>/g, "");
    if (!message.trim()) {
      messageError.textContent = "Отзыв не может быть пустым";
      hasErrors = true;
    } else if (message !== strippedMessage) {
      messageError.textContent = "Текст отзыва не должен содержать HTML";
      hasErrors = true;
    }

    if (hasErrors) {
      event.preventDefault(); // Предотвращаем отправку формы
    }
  });
});
