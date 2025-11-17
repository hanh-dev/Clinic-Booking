// Display all the slots that a specific day have
let datenew = null;
let slot = null;
function myfuncAvalialbe(slots, selected, date) {
  slot = null;
  datenew = date;
  console.log("Raw slots data:", slots);
  const allDays = document.querySelectorAll(".book-slots-day-item");
  allDays.forEach((day) => {
    day.style.backgroundColor = "";
    day.style.color = "";
  });
  // Hiển thị những ngày nào không có lịch hẹn với background khác
  selected.style.backgroundColor = "#00bfbf";
  selected.style.color = "#fff";
  const resultDiv = document.getElementById("result");
  const slotList = JSON.parse(slots).map(normalizeTime);
  const timeType = [
    "10:00 am",
    "10:30 am",
    "11:00 am",
    "11:30 am",
    "12:00 pm",
    "12:30 pm",
    "1:00 pm",
    "1:30 pm",
    "2:00 pm",
    "2:30 pm",
    "3:00 pm",
    "3:30 pm",
    "4:00 pm",
    "4:30 pm",
    "5:00 pm",
    "5:30 pm",
  ].map(normalizeTime);

  resultDiv.innerHTML = "";
  timeType.forEach((data) => {
    const p = document.createElement("p");

    if (slotList.includes(data)) {
      p.className = "book-slot_timevalue";
      p.textContent = data;
      p.onclick = () => selectSlot(p);
    } else {
      p.className = "book-slot_isBooked";
      p.textContent = data;
    }

    resultDiv.appendChild(p);
  });
}

const normalizeTime = (time) => time.trim().toLowerCase().replace(/^0/, "");
//
function selectSlot(selectedP) {
  slot = selectedP.textContent;
  console.log("Slot selected", slot);
  const allSlots = document.querySelectorAll("#result p");

  allSlots.forEach((slot) => {
    slot.style.backgroundColor = "";
    slot.style.color = "";
  });
  if (selectedP.style.backgroundColor == "#00bfbf") {
    selectedP.style.backgroundColor = "";
    selectedP.style.color = "#00174f";
  } else {
    selectedP.style.backgroundColor = "#00bfbf";
    selectedP.style.color = "#fff";
  }
}

function bookAppointment(id) {
  if (id === "") {
    alert("Please login before booking");
    return;
  }
  if (!datenew || !slot) {
    alert("Please select day/slot");
    return;
  }
  if (
    typeof datenew === "string" &&
    datenew.startsWith('"') &&
    datenew.endsWith('"')
  ) {
    datenew = JSON.parse(datenew);
  } else {
    console.warn("datenew is not valid JSON. Keeping as is:", datenew);
  }
  const dateInput = document.getElementById("date");
  // Data for slot
  const slotInput = document.getElementById("slot");
  slotInput.value = slot;
  console.log("New date", datenew);
  dateInput.value = datenew;
  const modalElement = new bootstrap.Modal(document.getElementById("model"));
  modalElement.show();
}

function redirectToPayment(doctorId) {
  const idDate = document.getElementById("date").value;
  const idSlot = document.getElementById("slot").value;
  const patientName = document.getElementById("name").value;
  const description = document.getElementById("desc").value;

  const requestData = {
    doctorId: doctorId,
    idDate: idDate,
    idSlot: idSlot,
    patientName: patientName,
    description: description,
  };
  fetch("http://localhost/PHP_CLINIC/Clinic-Booking/public/js/handleData.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(requestData),
  })
    .then((res) => res.text())
    .then((data) => {
      console.log("Response data:", data);
      try {
        const jsonResponse = JSON.parse(data);
        console.log(jsonResponse);
        location.href =
          "/PHP_CLINIC/Clinic-Booking/DoctorDetail/payment/" + doctorId;
      } catch (error) {
        console.error("Failed to parse JSON:", error);
      }
    })
    .catch((error) => console.error("Error:", error));
}
