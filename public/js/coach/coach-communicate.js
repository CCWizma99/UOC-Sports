document.addEventListener("DOMContentLoaded", () => {
  const sendBtn = document.getElementById("sendBtn");
  const clearBtn = document.getElementById("clearBtn");
  const recipient = document.getElementById("recipient");
  const title = document.getElementById("title");
  const message = document.getElementById("message");
  const statusMsg = document.getElementById("statusMsg");
  const messagesList = document.getElementById("messagesList");

  // Send button
  sendBtn.addEventListener("click", () => {
    if (recipient.value && title.value && message.value) {
      const li = document.createElement("li");

      const senderSpan = document.createElement("span");
      senderSpan.className = "sender";
      senderSpan.textContent = "You → " + recipient.value;

      const textSpan = document.createElement("span");
      textSpan.className = "text";
      textSpan.textContent = `${title.value}: ${message.value}`;

      const dateSpan = document.createElement("span");
      dateSpan.className = "date";
      const today = new Date();
      dateSpan.textContent = today.toLocaleDateString("en-GB", {
        day: "2-digit",
        month: "short"
      });

      const deleteSpan = document.createElement("span");
      deleteSpan.className = "delete";
      deleteSpan.textContent = "×";
      deleteSpan.title = "Delete";

      deleteSpan.addEventListener("click", () => {
        li.remove();
      });

      li.appendChild(senderSpan);
      li.appendChild(textSpan);
      li.appendChild(dateSpan);
      li.appendChild(deleteSpan);

      messagesList.prepend(li);

      // Clear form
      recipient.value = "";
      title.value = "";
      message.value = "";
      statusMsg.textContent = "Message Sent!";
      setTimeout(() => (statusMsg.textContent = ""), 2000);
    } else {
      statusMsg.textContent = "Please fill in all fields!";
      statusMsg.style.color = "red";
      setTimeout(() => {
        statusMsg.textContent = "";
        statusMsg.style.color = "green";
      }, 2000);
    }
  });

  // Clear button
  clearBtn.addEventListener("click", () => {
    recipient.value = "";
    title.value = "";
    message.value = "";
    statusMsg.textContent = "";
  });

  // Delete for existing messages
  document.querySelectorAll(".delete").forEach((btn) => {
    btn.addEventListener("click", (e) => {
      e.target.parentElement.remove();
    });
  });
});
