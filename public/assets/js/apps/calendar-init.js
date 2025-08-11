
/*========Calender Js=========*/
/*==========================*/

document.addEventListener("DOMContentLoaded", function () {
  // Ensure the Nepali Date Converter library is loaded
  if (typeof NepaliDate !== "function") {
    console.error(
      "Nepali Date Converter library not found. Please include it."
    );
    // Optionally disable calendar initialization or show an error message
    // return;
    alert(
      "Error: Nepali Date Converter library is missing. Calendar may not display Nepali dates correctly."
    );
    // Initialize a dummy object to prevent further errors if you proceed
    window.NepaliDate = function (date) {
      return {
        getBS: function () {
          console.warn(
            "NepaliDate dummy used. Conversion unavailable."
          );
          const d = date instanceof Date ? date : new Date();
          return {
            year: d.getFullYear(),
            month: d.getMonth() + 1,
            date: d.getDate(),
          };
        },
      };
    };
  }

  /*=================*/
  // Nepali Date Helpers
  /*=================*/
  const bsMonths = [
    "Baishakh", // १
    "Jestha",   // २
    "Ashadh",   // ३
    "Shrawan",  // ४
    "Bhadra",   // ५
    "Ashwin",   // ६
    "Kartik",   // ७
    "Mangsir",  // ८
    "Poush",    // ९
    "Magh",     // १०
    "Falgun",   // ११
    "Chaitra",  // १२
  ];

  // Function to convert Gregorian Date object to BS object
  function getBSDate(gregorianDate) {
    if (!gregorianDate || !(gregorianDate instanceof Date)) {
        console.warn("Invalid date passed to getBSDate:", gregorianDate);
        // Return a default or null structure to avoid errors downstream
        return null;
    }
    try {
        // Ensure the library is available (check added at the top)
        const nepaliDate = new NepaliDate(gregorianDate);
        return nepaliDate.getBS(); // Returns object like { year: 2081, month: 1, date: 1 }
    } catch (e) {
        console.error("Error converting date to BS:", gregorianDate, e);
        return null; // Handle conversion errors gracefully
    }
  }

  // Function to convert English digits to Nepali digits
  function toNepaliDigits(numStr) {
     if (numStr === null || numStr === undefined) return '';
    const englishDigits = ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9"];
    const nepaliDigits = ["०", "१", "२", "३", "४", "५", "६", "७", "८", "९"];
    let nepaliStr = String(numStr);
    englishDigits.forEach((digit, index) => {
      nepaliStr = nepaliStr.replace(
        new RegExp(digit, "g"),
        nepaliDigits[index]
      );
    });
    return nepaliStr;
  }

  /*=================*/
  //  Calendar Date variable (Gregorian for setup)
  /*=================*/
  var newDate = new Date(); // Use Gregorian for FullCalendar's internal logic

  // This function remains the same as it's used for setting up initial Gregorian event dates
  function getDynamicMonth() {
    getMonthValue = newDate.getMonth();
    _getUpdatedMonthValue = getMonthValue + 1;
    if (_getUpdatedMonthValue < 10) {
      return `0${_getUpdatedMonthValue}`;
    } else {
      return `${_getUpdatedMonthValue}`;
    }
  }

  /*=================*/
  // Calender Modal Elements (No change needed here)
  /*=================*/
  var getModalTitleEl = document.querySelector("#event-title");
  var getModalStartDateEl = document.querySelector("#event-start-date");
  var getModalEndDateEl = document.querySelector("#event-end-date");
  var getModalAddBtnEl = document.querySelector(".btn-add-event");
  var getModalUpdateBtnEl = document.querySelector(".btn-update-event");
  var calendarsEvents = {
    Danger: "danger",
    Success: "success",
    Primary: "primary",
    Warning: "warning",
  };

  /*=====================*/
  // Calendar Elements and options
  /*=====================*/
  var calendarEl = document.querySelector("#calendar");
  var checkWidowWidth = function () {
    return window.innerWidth <= 1199;
  };

  // Keep toolbar buttons in English as requested
  var calendarHeaderToolbar = {
    left: "prev,next today addEventButton", // Keep 'today' button functional
    center: "title", // Title will be dynamically updated
    right: "dayGridMonth,timeGridWeek,timeGridDay,listWeek", // Added listWeek back
  };

  // IMPORTANT: Event data MUST use standard Gregorian ISO dates (YYYY-MM-DD)
  // FullCalendar uses these for calculations. We only change the *display*.
  var calendarEventsList = [
    {
      id: 1,
      title: "Event Conf.",
      start: `${newDate.getFullYear()}-${getDynamicMonth()}-01`,
      extendedProps: { calendar: "Danger" },
    },
    {
      id: 2,
      title: "Seminar #4",
      start: `${newDate.getFullYear()}-${getDynamicMonth()}-07`,
      end: `${newDate.getFullYear()}-${getDynamicMonth()}-10`,
      extendedProps: { calendar: "Success" },
    },
    {
      groupId: "999",
      id: 3,
      title: "Meeting #5",
      start: `${newDate.getFullYear()}-${getDynamicMonth()}-09T16:00:00`,
      extendedProps: { calendar: "Primary" },
    },
    {
      groupId: "999",
      id: 4,
      title: "Submission #1",
      start: `${newDate.getFullYear()}-${getDynamicMonth()}-16T16:00:00`,
      extendedProps: { calendar: "Warning" },
    },
    {
      id: 5,
      title: "Seminar #6",
      start: `${newDate.getFullYear()}-${getDynamicMonth()}-11`,
      end: `${newDate.getFullYear()}-${getDynamicMonth()}-13`,
      extendedProps: { calendar: "Danger" },
    },
    {
      id: 6,
      title: "Meeting 3",
      start: `${newDate.getFullYear()}-${getDynamicMonth()}-12T10:30:00`,
      end: `${newDate.getFullYear()}-${getDynamicMonth()}-12T12:30:00`,
      extendedProps: { calendar: "Success" },
    },
    {
      id: 7,
      title: "Meetup #",
      start: `${newDate.getFullYear()}-${getDynamicMonth()}-12T12:00:00`,
      extendedProps: { calendar: "Primary" },
    },
    {
      id: 8,
      title: "Submission",
      start: `${newDate.getFullYear()}-${getDynamicMonth()}-12T14:30:00`,
      extendedProps: { calendar: "Warning" },
    },
    {
      id: 9,
      title: "Attend event",
      start: `${newDate.getFullYear()}-${getDynamicMonth()}-13T07:00:00`,
      extendedProps: { calendar: "Success" },
    },
    {
      id: 10,
      title: "Project submission #2",
      start: `${newDate.getFullYear()}-${getDynamicMonth()}-28`,
      extendedProps: { calendar: "Primary" },
    },
  ];

  /*=====================*/
  // Calendar Select fn. (Keep using Gregorian for input elements)
  /*=====================*/
  var calendarSelect = function (info) {
    getModalAddBtnEl.style.display = "block";
    getModalUpdateBtnEl.style.display = "none";
    myModal.show();
    // Modal inputs should work with standard YYYY-MM-DD format
    getModalStartDateEl.value = info.startStr.slice(0, 10); // Keep Gregorian
    getModalEndDateEl.value = info.endStr.slice(0, 10); // Keep Gregorian
  };

  /*=====================*/
  // Calendar AddEvent fn. (Keep using Gregorian for input elements)
  /*=====================*/
  var calendarAddEvent = function () {
    var currentDate = new Date();
    var yyyy = currentDate.getFullYear();
    var mm = String(currentDate.getMonth() + 1).padStart(2, "0");
    var dd = String(currentDate.getDate()).padStart(2, "0");
    var combineDate = `${yyyy}-${mm}-${dd}`; // Use YYYY-MM-DD for input

    getModalAddBtnEl.style.display = "block";
    getModalUpdateBtnEl.style.display = "none";
    myModal.show();
    getModalStartDateEl.value = combineDate; // Set Gregorian default
    getModalEndDateEl.value = ""; // Clear end date
    getModalTitleEl.value = ""; // Clear title
  };

  /*=====================*/
  // Calender Event Function (Keep using Gregorian for input elements)
  /*=====================*/
  var calendarEventClick = function (info) {
    var eventObj = info.event;

    if (eventObj.url) {
      window.open(eventObj.url);
      info.jsEvent.preventDefault();
    } else {
      var getModalEventId = eventObj.id; // Use event.id which is stable
      var getModalEventLevel = eventObj.extendedProps.calendar;
      var getModalCheckedRadioBtnEl = document.querySelector(
        `input[value="${getModalEventLevel}"]`
      );

      getModalTitleEl.value = eventObj.title;
      // Use FullCalendar's formatting utilities or slice Gregorian dates for inputs
      // formatIso requires a Date object
      const startDate = eventObj.start ? calendar.formatIso(eventObj.start, { omitTime: true }) : '';
      // End date in FullCalendar might be null or exclusive. Handle carefully.
      // If event is all day and has end, formatIso usually gives day after. Adjust if needed.
      let endDate = '';
       if (eventObj.end) {
          if (eventObj.allDay) {
              // For allDay events, FullCalendar's end is exclusive. Subtract a day for inclusive display.
              let inclusiveEnd = new Date(eventObj.end);
              inclusiveEnd.setDate(inclusiveEnd.getDate() - 1);
              endDate = calendar.formatIso(inclusiveEnd, { omitTime: true });
          } else {
               endDate = calendar.formatIso(eventObj.end, { omitTime: true });
          }
       }


      getModalStartDateEl.value = startDate; // Keep input Gregorian
      getModalEndDateEl.value = endDate;   // Keep input Gregorian

      if (getModalCheckedRadioBtnEl) {
          getModalCheckedRadioBtnEl.checked = true;
      }
      getModalUpdateBtnEl.setAttribute("data-fc-event-id", getModalEventId); // Use data-fc-event-id
      getModalAddBtnEl.style.display = "none";
      getModalUpdateBtnEl.style.display = "block";
      myModal.show();
    }
  };

  /*=====================*/
  // Active Calender
  /*=====================*/
  var calendar = new FullCalendar.Calendar(calendarEl, {
    // --- Nepali Date customizations ---
    locale: 'en', // Keep locale 'en' for button text etc., override display manually
    dayCellContent: function (info) {
      // Display Nepali date number in day grid cells
      const bsDateInfo = getBSDate(info.date);
       if (bsDateInfo) {
           return { html: `<span class="nepali-date">${toNepaliDigits(bsDateInfo.date)}</span>` };
       } else {
           // Fallback to default if conversion fails
           return { domNodes: [document.createTextNode(info.dayNumberText.replace('日', ''))] };
       }
    },
     datesSet: function(info) {
        // Update the calendar title with BS Month and Year
        const view = info.view;
        const startDate = view.activeStart;
        const endDate = view.currentEnd; // Use currentEnd which is exclusive

        const bsStartDate = getBSDate(startDate);

        // Determine the date range for the title based on the view
        let titleDate = startDate; // Default to start date for title month/year

        if (view.type === 'dayGridMonth') {
            // Month view title typically shows the BS month corresponding to the start of the Gregorian month view
            titleDate = startDate;
        } else if (view.type === 'timeGridWeek' || view.type === 'listWeek') {
             // Week view might span two BS months, show range or primary month
             // Using start date's BS month for simplicity here
             titleDate = startDate;
        } else if (view.type === 'timeGridDay') {
            // Day view shows the specific day
             titleDate = startDate;
        }
        // Find the middle date of the current view to potentially get a more representative month/year
        // const middleDate = new Date(startDate.getTime() + (endDate.getTime() - startDate.getTime()) / 2);
        // const bsMiddleDate = getBSDate(middleDate);

         const bsTitleDateInfo = getBSDate(titleDate);

        const titleEl = document.querySelector('#calendar .fc-toolbar-title');
        if (titleEl && bsTitleDateInfo) {
            const bsMonthName = bsMonths[bsTitleDateInfo.month - 1];
            const bsYearNepali = toNepaliDigits(bsTitleDateInfo.year);
            titleEl.textContent = `${bsMonthName} ${bsYearNepali}`;
        } else if (titleEl) {
             // Fallback to default title if conversion fails
             titleEl.textContent = view.title; // Default FullCalendar title
        }
     },
     // You might also want to customize day headers if needed (e.g., in timeGridWeek)
     // dayHeaderContent: function(info) {
     //    const bsDateInfo = getBSDate(info.date);
     //    if (bsDateInfo) {
     //        return { html: `${info.text} <span class="nepali-date-header">${toNepaliDigits(bsDateInfo.date)}</span>` }; // Example: "Mon 15" -> "Mon १५"
     //    }
     // },


    // --- Original Options (slightly modified) ---
    selectable: true,
    height: checkWidowWidth() ? 900 : 1052,
    initialView: checkWidowWidth() ? "listWeek" : "dayGridMonth",
    initialDate: newDate.toISOString().slice(0, 10), // Use current Gregorian date as initial
    headerToolbar: calendarHeaderToolbar,
    events: calendarEventsList, // Use Gregorian dates here
    select: calendarSelect, // Handles date range selection
    unselect: function () {
      // console.log("unselected");
    },
    customButtons: {
      addEventButton: {
        text: "Add Event", // Keep English text
        click: calendarAddEvent,
      },
    },
    eventClassNames: function ({ event: calendarEvent }) {
      const colorName = calendarEvent.extendedProps.calendar;
      const colorValue = calendarsEvents[colorName] || 'primary'; // Default color
      return ["event-fc-color", `fc-bg-${colorValue}`];
    },
    eventClick: calendarEventClick, // Handles clicking on an existing event
    windowResize: function (arg) {
        calendar.setOption('height', checkWidowWidth() ? 900 : 1052);
        // Optional: Change view on resize, but maybe not necessary if user manually selects views
        // if (checkWidowWidth() && calendar.view.type !== 'listWeek') {
        //   calendar.changeView('listWeek');
        // } else if (!checkWidowWidth() && calendar.view.type === 'listWeek') {
        //   calendar.changeView('dayGridMonth');
        // }
    },
    // IMPORTANT: Keep event dates handled internally as Gregorian
    // eventDataTransform: function(eventData) {
    //    // NO BS conversion needed here. Input data MUST be Gregorian.
    //    return eventData;
    // },
    // eventDidMount: function(info) {
        // Optional: If you need to modify the event text display itself to show BS dates
        // const event = info.event;
        // const el = info.el;
        // const bsStart = event.start ? getBSDate(event.start) : null;
        // const bsEnd = event.end ? getBSDate(event.end) : null;
        // You could manipulate el.innerHTML here, but eventContent is often cleaner
    // },
    // eventContent: function(info) {
       // Example: Modify how event text is displayed (optional)
       // const event = info.event;
       // const bsStart = event.start ? getBSDate(event.start) : null;
       // let displayTitle = event.title;
       // if (bsStart) {
       //     displayTitle += ` (Starts: ${toNepaliDigits(bsStart.date)} ${bsMonths[bsStart.month-1]})`;
       // }
       // return { html: `<div class="fc-event-title">${displayTitle}</div>` };
    // }
  });

  /*=====================*/
  // Update Calendar Event (Uses Gregorian dates from modal)
  /*=====================*/
  getModalUpdateBtnEl.addEventListener("click", function () {
    var getEventId = this.dataset.fcEventId; // Use the stored event ID
    var getTitleUpdatedValue = getModalTitleEl.value;
    var setModalStartDateValue = getModalStartDateEl.value; // YYYY-MM-DD
    var setModalEndDateValue = getModalEndDateEl.value; // YYYY-MM-DD
    var getEvent = calendar.getEventById(getEventId); // Fetch event by ID

    if (!getEvent) {
        console.error("Could not find event with ID:", getEventId);
        myModal.hide();
        return;
    }

    var getModalUpdatedCheckedRadioBtnEl = document.querySelector(
      'input[name="event-level"]:checked'
    );
    var getModalUpdatedCheckedRadioBtnValue =
      getModalUpdatedCheckedRadioBtnEl?.value || // Use optional chaining
      getEvent.extendedProps.calendar; // Keep existing if none selected

    // Use setProp and setDates for cleaner updates
    getEvent.setProp("title", getTitleUpdatedValue);
    getEvent.setExtendedProp("calendar", getModalUpdatedCheckedRadioBtnValue);

     // Handle dates carefully: FullCalendar needs null for no end date.
     // Add time if your input could potentially include it, otherwise just the date.
     let endValue = setModalEndDateValue || null; // Use null if empty

     // Adjust end date for all-day events if necessary
     // If the input is just a date (YYYY-MM-DD) and it's an all-day event,
     // FullCalendar expects the 'end' to be the *day after* the last day.
     if (getEvent.allDay && endValue) {
         try {
            let exclusiveEnd = new Date(endValue + 'T00:00:00'); // Parse as local time start of day
            exclusiveEnd.setDate(exclusiveEnd.getDate() + 1);
            endValue = exclusiveEnd.toISOString().split('T')[0]; // Format back to YYYY-MM-DD
         } catch(e) {
             console.error("Error adjusting end date for all-day event:", e);
             // Decide how to handle error: use original end, or clear it?
             endValue = null; // Safer to clear if parsing fails
         }
     }

    getEvent.setDates(setModalStartDateValue, endValue, { allDay: getEvent.allDay }); // Preserve allDay property

    myModal.hide();
  });

  /*=====================*/
  // Add Calendar Event (Uses Gregorian dates from modal)
  /*=====================*/
  getModalAddBtnEl.addEventListener("click", function () {
    var getModalCheckedRadioBtnEl = document.querySelector(
      'input[name="event-level"]:checked'
    );

    var getTitleValue = getModalTitleEl.value;
    var setModalStartDateValue = getModalStartDateEl.value; // YYYY-MM-DD
    var setModalEndDateValue = getModalEndDateEl.value; // YYYY-MM-DD
    var getModalCheckedRadioBtnValue =
      getModalCheckedRadioBtnEl?.value || "Primary"; // Default category

    // Basic validation
    if (!getTitleValue || !setModalStartDateValue) {
        alert("Please enter at least a title and start date.");
        return;
    }

    // Determine if it's all day (no time specified usually means all day)
    // For simplicity, assume allDay if no end date or end date is same as start date without time
    // More robust check would involve time inputs if you add them
    const isAllDay = !setModalEndDateValue || setModalEndDateValue === setModalStartDateValue;

    let eventEnd = setModalEndDateValue || null;

    // Adjust end date for all-day events (exclusive end)
    if (isAllDay && eventEnd) {
        try {
            let exclusiveEnd = new Date(eventEnd + 'T00:00:00');
            exclusiveEnd.setDate(exclusiveEnd.getDate() + 1);
            eventEnd = exclusiveEnd.toISOString().split('T')[0];
        } catch(e) {
            console.error("Error adjusting end date for new all-day event:", e);
            eventEnd = null; // Clear end date on error
        }
    }


    calendar.addEvent({
      // id: generateUniqueId(), // Good practice to generate a unique ID
      title: getTitleValue,
      start: setModalStartDateValue, // Gregorian
      end: eventEnd, // Gregorian (adjusted if allDay)
      allDay: isAllDay,
      extendedProps: { calendar: getModalCheckedRadioBtnValue },
    });

    myModal.hide();
  });

  /*=====================*/
  // Calendar Init & Modal Setup
  /*=====================*/
  calendar.render();
  var myModal = new bootstrap.Modal(document.getElementById("eventModal"));

  // Clear modal on hide
  document
    .getElementById("eventModal")
    .addEventListener("hidden.bs.modal", function (event) {
      getModalTitleEl.value = "";
      getModalStartDateEl.value = "";
      getModalEndDateEl.value = "";
      var getModalIfCheckedRadioBtnEl = document.querySelector(
        'input[name="event-level"]:checked'
      );
      if (getModalIfCheckedRadioBtnEl) { // Check if not null
        getModalIfCheckedRadioBtnEl.checked = false;
      }
      // Reset update button attribute
      getModalUpdateBtnEl.removeAttribute("data-fc-event-id");
    });

  // Add Optional CSS for Nepali dates if needed
  const style = document.createElement('style');
  style.textContent = `
    .nepali-date {
        /* Add any specific styling for Nepali dates here */
        /* font-weight: bold; */
         display: inline-block; /* Ensure it takes space */
         min-width: 1.5em; /* Give some space */
         text-align: center;
    }
    .fc-daygrid-day-number {
        /* Ensure alignment works */
        padding-right: 5px !important; /* Adjust as needed */
    }
     .fc .fc-toolbar-title {
        /* Ensure title has enough space */
        min-width: 150px;
        text-align: center;
    }
  `;
  document.head.appendChild(style);

}); // End DOMContentLoaded