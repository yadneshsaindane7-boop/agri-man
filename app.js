let currentLanguage = 'en'; // Default language

// Function to convert Fahrenheit to Celsius
const toCelsius = f => Math.round((f - 32) * 5 / 9);

// Application Data with translation keys
let appData = {
  crops: [
    {"id": 1, "nameKey": "crop_tomato", "plantingMonths": ["Feb", "Mar", "Apr", "Jul", "Aug"], "daysToMaturity": 75, "plantingDepth": "0.25 inch", "spacing": "18-24 inches", "soilTemp": "60-70°F", "tips": "Start indoors 6-8 weeks before transplanting", "family": "Nightshade"},
    {"id": 2, "nameKey": "crop_corn", "plantingMonths": ["Apr", "May", "Jun"], "daysToMaturity": 90, "plantingDepth": "1-2 inches", "spacing": "8-12 inches", "soilTemp": "50-55°F", "tips": "Plant after soil temperature consistently reaches 50°F", "family": "Grass"},
    {"id": 3, "nameKey": "crop_wheat", "plantingMonths": ["Oct", "Nov"], "daysToMaturity": 200, "plantingDepth": "1-1.5 inches", "spacing": "1-1.5 inches", "soilTemp": "45-55°F", "tips": "Winter variety for most regions", "family": "Grass"},
    {"id": 4, "nameKey": "crop_potato", "plantingMonths": ["Mar", "Apr", "May"], "daysToMaturity": 90, "plantingDepth": "4 inches", "spacing": "12-15 inches", "soilTemp": "45-55°F", "tips": "Plant seed potatoes 2-3 weeks before last frost", "family": "Nightshade"},
    {"id": 5, "nameKey": "crop_carrot", "plantingMonths": ["Mar", "Apr", "May", "Aug", "Sep"], "daysToMaturity": 70, "plantingDepth": "0.25 inch", "spacing": "2-3 inches", "soilTemp": "45-65°F", "tips": "Successive plantings every 2-3 weeks", "family": "Apiaceae"},
    {"id": 6, "nameKey": "crop_lettuce", "plantingMonths": ["Mar", "Apr", "Aug", "Sep"], "daysToMaturity": 45, "plantingDepth": "0.25 inch", "spacing": "6-8 inches", "soilTemp": "35-65°F", "tips": "Cool season crop, bolts in hot weather", "family": "Asteraceae"},
    {"id": 7, "nameKey": "crop_beans", "plantingMonths": ["May", "Jun", "Jul"], "daysToMaturity": 55, "plantingDepth": "1-1.5 inches", "spacing": "4-6 inches", "soilTemp": "60-70°F", "tips": "Wait until soil is warm to prevent rot", "family": "Legume"},
    {"id": 8, "nameKey": "crop_peas", "plantingMonths": ["Mar", "Apr", "Aug", "Sep"], "daysToMaturity": 60, "plantingDepth": "1-2 inches", "spacing": "2-4 inches", "soilTemp": "40-60°F", "tips": "Plant as early as soil can be worked", "family": "Legume"},
    {"id": 9, "nameKey": "crop_cabbage", "plantingMonths": ["Mar", "Apr", "Jul", "Aug"], "daysToMaturity": 80, "plantingDepth": "0.25 inch", "spacing": "12-18 inches", "soilTemp": "45-65°F", "tips": "Start indoors for spring crop", "family": "Brassica"},
    {"id": 10, "nameKey": "crop_broccoli", "plantingMonths": ["Mar", "Apr", "Aug"], "daysToMaturity": 70, "plantingDepth": "0.25 inch", "spacing": "12-18 inches", "soilTemp": "45-65°F", "tips": "Needs cool weather to form heads", "family": "Brassica"}
  ],
  tasks: [
    {"id": 1, "titleKey": "task_plant_tomatoes", "category": "Planting", "date": "2025-03-15", "priority": "High", "status": "Pending"},
    {"id": 2, "titleKey": "task_fertilize_corn", "category": "Fertilizing", "date": "2025-04-10", "priority": "Medium", "status": "Pending"},
    {"id": 3, "titleKey": "task_harvest_wheat", "category": "Harvesting", "date": "2025-06-20", "priority": "High", "status": "Pending"},
    {"id": 4, "titleKey": "task_water_garden", "category": "Watering", "date": "2025-10-08", "priority": "Medium", "status": "Pending"},
    {"id": 5, "titleKey": "task_weed_carrots", "category": "Weeding", "date": "2025-10-10", "priority": "Low", "status": "Pending"}
  ],
  events: [
    {"id": 1, "titleKey": "event_plant_tomatoes", "date": "2025-03-15", "type": "planting"},
    {"id": 2, "titleKey": "event_harvest_corn", "date": "2025-08-20", "type": "harvest"},
    {"id": 3, "titleKey": "event_fertilize_fields", "date": "2025-04-10", "type": "maintenance"},
    {"id": 4, "titleKey": "event_plant_wheat", "date": "2025-10-15", "type": "planting"},
    {"id": 5, "titleKey": "event_harvest_potatoes", "date": "2025-09-10", "type": "harvest"}
  ],
  weather: {
    // --- TEMPERATURES CONVERTED TO CELSIUS ---
    current: {"temp": toCelsius(72), "condition": "Sunny", "humidity": 45, "wind": 8},
    forecast: [
      {"day": "Today", "high": toCelsius(75), "low": toCelsius(58), "condition": "Sunny"},
      {"day": "Tomorrow", "high": toCelsius(73), "low": toCelsius(56), "condition": "Partly Cloudy"},
      {"day": "Wed", "high": toCelsius(68), "low": toCelsius(54), "condition": "Rainy"},
      {"day": "Thu", "high": toCelsius(70), "low": toCelsius(55), "condition": "Cloudy"},
      {"day": "Fri", "high": toCelsius(72), "low": toCelsius(57), "condition": "Sunny"},
      {"day": "Sat", "high": toCelsius(74), "low": toCelsius(59), "condition": "Sunny"},
      {"day": "Sun", "high": toCelsius(76), "low": toCelsius(61), "condition": "Partly Cloudy"}
    ]
  },
  rotation: {},
  nextId: {
    crop: 11,
    task: 6,
    event: 6
  }
};

let currentDate = new Date();
let currentSection = 'dashboard';
let selectedField = null;
let selectedYear = null;

// DOM Elements
const menuItems = document.querySelectorAll('.menu-item');
const sections = document.querySelectorAll('.section');

// Language Switcher Function
function switchLanguage(lang) {
  currentLanguage = lang;

  document.querySelectorAll('[data-lang]').forEach(element => {
    const key = element.getAttribute('data-lang');
    if (translations[lang] && translations[lang][key]) {
      if (element.tagName === 'INPUT' && element.placeholder) {
        element.placeholder = translations[lang][key];
      } else {
        element.textContent = translations[lang][key];
      }
    }
  });

  document.querySelectorAll('.lang-btn').forEach(btn => {
    btn.classList.remove('active');
    if (btn.getAttribute('data-lang-code') === lang) {
      btn.classList.add('active');
    }
  });

  // Re-render the current section to update dynamic content
  switchSection(currentSection, true);
}

// Initialize Application
document.addEventListener('DOMContentLoaded', function() {
  initializeNavigation();
  initializeDashboard();
  initializeCalendar();
  initializeCrops();
  initializeTasks();
  initializeWeather();
  initializeRotation();
  initializeReports();
  initializeModals();

  // Initialize Language Switcher
  document.getElementById('lang-en').addEventListener('click', () => switchLanguage('en'));
  document.getElementById('lang-mr').addEventListener('click', () => switchLanguage('mr'));

  // Set initial language and render the dashboard
  switchLanguage('en');

  const today = currentDate.toISOString().split('T')[0];
  document.getElementById('event-date').value = today;
  document.getElementById('task-date').value = today;
});

// Navigation
function initializeNavigation() {
  menuItems.forEach(item => {
    item.addEventListener('click', (e) => {
      e.preventDefault();
      const section = item.getAttribute('data-section');
      switchSection(section, false);
    });
  });
}

function switchSection(sectionName, isLanguageSwitch = false) {
  currentSection = sectionName;

  if (!isLanguageSwitch) {
    menuItems.forEach(item => item.classList.remove('active'));
    document.querySelector(`[data-section="${sectionName}"]`).classList.add('active');

    sections.forEach(section => section.classList.remove('active'));
    document.getElementById(sectionName).classList.add('active');
  }

  // Refresh section content
  switch(sectionName) {
    case 'dashboard': updateDashboard(); break;
    case 'calendar': renderCalendar(); break;
    case 'crops': renderCrops(); break;
    case 'tasks': renderTasks(); break;
    case 'weather': updateWeather(); break;
    case 'rotation': updateRotationText(); break;
    case 'reports': updateReports(); break;
  }
}

// Dashboard
function initializeDashboard() { updateDashboard(); }

function updateDashboard() {
  document.getElementById('total-crops').textContent = appData.crops.length;
  document.getElementById('pending-tasks').textContent = appData.tasks.filter(t => t.status === 'Pending').length;
  // --- UPDATED TO CELSIUS ---
  document.getElementById('current-temp').textContent = appData.weather.current.temp + '°C';

  const weekFromNow = new Date();
  weekFromNow.setDate(weekFromNow.getDate() + 7);
  const upcomingEvents = appData.events.filter(event => {
    const eventDate = new Date(event.date);
    return eventDate >= currentDate && eventDate <= weekFromNow;
  }).length;
  document.getElementById('upcoming-events').textContent = upcomingEvents;

  const upcomingTasks = appData.tasks
    .filter(task => task.status === 'Pending')
    .sort((a, b) => new Date(a.date) - new Date(b.date))
    .slice(0, 5);

  const tasksList = document.getElementById('upcoming-tasks-list');
  tasksList.innerHTML = '';

  if (upcomingTasks.length === 0) {
    tasksList.innerHTML = `<div class="empty-state" data-lang="noUpcomingTasks">${translations[currentLanguage].noUpcomingTasks}</div>`;
  } else {
    upcomingTasks.forEach(task => {
      const taskTitle = translations[currentLanguage][task.titleKey] || task.titleKey;
      const taskElement = document.createElement('div');
      taskElement.className = 'task-list-item';
      taskElement.innerHTML = `
        <div class="task-list-content">
          <div class="task-list-title">${taskTitle}</div>
          <div class="task-list-meta">${formatDate(task.date)} • ${task.category}</div>
        </div>
        <span class="task-priority ${task.priority.toLowerCase()}">${task.priority}</span>`;
      tasksList.appendChild(taskElement);
    });
  }

  const seasonOverview = document.getElementById('season-overview');
  // ... (Season overview logic remains the same, as it's not text-heavy)
}

// Calendar
function initializeCalendar() {
  document.getElementById('prev-month').addEventListener('click', () => { currentDate.setMonth(currentDate.getMonth() - 1); renderCalendar(); });
  document.getElementById('next-month').addEventListener('click', () => { currentDate.setMonth(currentDate.getMonth() + 1); renderCalendar(); });
  renderCalendar();
}

function renderCalendar() {
  const monthYear = currentDate.toLocaleString(`${currentLanguage}-IN`, { month: 'long', year: 'numeric' });
  document.getElementById('current-month-year').textContent = monthYear;

  const calendarGrid = document.getElementById('calendar-grid');
  calendarGrid.innerHTML = '';

  const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
  days.forEach(day => {
    const dayHeader = document.createElement('div');
    dayHeader.className = 'calendar-header';
    dayHeader.textContent = day;
    calendarGrid.appendChild(dayHeader);
  });

  const firstDay = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
  const lastDay = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0);
  const firstDayOfWeek = firstDay.getDay();
  const daysInMonth = lastDay.getDate();

  const prevMonth = new Date(currentDate.getFullYear(), currentDate.getMonth() - 1, 0);
  for (let i = firstDayOfWeek - 1; i >= 0; i--) { calendarGrid.appendChild(createCalendarDay(prevMonth.getDate() - i, true)); }
  for (let day = 1; day <= daysInMonth; day++) { calendarGrid.appendChild(createCalendarDay(day, false)); }
  const totalCells = calendarGrid.children.length - 7;
  const remainingCells = 42 - totalCells;
  for (let day = 1; day <= remainingCells; day++) { calendarGrid.appendChild(createCalendarDay(day, true)); }
}

function createCalendarDay(dayNumber, isOtherMonth) {
  const dayElement = document.createElement('div');
  dayElement.className = `calendar-day ${isOtherMonth ? 'other-month' : ''}`;
  dayElement.innerHTML = `<div class="day-number">${dayNumber}</div><div class="day-events"></div>`;

  if (!isOtherMonth) {
    const dayEvents = appData.events.filter(event => {
      const eventDate = new Date(event.date);
      return eventDate.getDate() === dayNumber && eventDate.getMonth() === currentDate.getMonth() && eventDate.getFullYear() === currentDate.getFullYear();
    });

    const eventsContainer = dayElement.querySelector('.day-events');
    dayEvents.forEach(event => {
      const eventTitle = translations[currentLanguage][event.titleKey] || event.titleKey;
      const eventElement = document.createElement('div');
      eventElement.className = `event-item ${event.type}`;
      eventElement.textContent = eventTitle;
      eventsContainer.appendChild(eventElement);
    });
  }

  dayElement.addEventListener('click', () => {
    if (!isOtherMonth) {
      const dayDate = new Date(currentDate.getFullYear(), currentDate.getMonth(), dayNumber);
      document.getElementById('event-date').value = dayDate.toISOString().split('T')[0];
      showModal('event-modal');
    }
  });

  return dayElement;
}

// Crops
function initializeCrops() {
  document.getElementById('crop-search').addEventListener('input', renderCrops);
  document.getElementById('crop-filter').addEventListener('change', renderCrops);
  const families = [...new Set(appData.crops.map(crop => crop.family))];
  const filterSelect = document.getElementById('crop-filter');
  // Clear existing options before populating
  filterSelect.innerHTML = `<option value="" data-lang="allFamilies">${translations[currentLanguage].allFamilies}</option>`;
  families.forEach(family => {
    const option = document.createElement('option');
    option.value = family;
    option.textContent = family;
    filterSelect.appendChild(option);
  });
  renderCrops();
}

function renderCrops() {
  const searchTerm = document.getElementById('crop-search').value.toLowerCase();
  const familyFilter = document.getElementById('crop-filter').value;

  const filteredCrops = appData.crops.filter(crop => {
    const cropName = (translations[currentLanguage][crop.nameKey] || crop.nameKey).toLowerCase();
    const matchesSearch = cropName.includes(searchTerm);
    const matchesFamily = !familyFilter || crop.family === familyFilter;
    return matchesSearch && matchesFamily;
  });

  const cropsGrid = document.getElementById('crops-grid');
  cropsGrid.innerHTML = '';

  filteredCrops.forEach(crop => {
    const cropName = translations[currentLanguage][crop.nameKey] || crop.nameKey;
    const cropCard = document.createElement('div');
    cropCard.className = 'crop-card';
    cropCard.innerHTML = `
      <div class="crop-header"><h3 class="crop-name">${cropName}</h3><span class="crop-family">${crop.family}</span></div>
      <div class="crop-details">
        <div class="crop-detail"><strong>Maturity:</strong> ${crop.daysToMaturity} days</div>
        <div class="crop-detail"><strong>Depth:</strong> ${crop.plantingDepth}</div>
        <div class="crop-detail"><strong>Spacing:</strong> ${crop.spacing}</div>
        <div class="crop-detail"><strong>Soil Temp:</strong> ${crop.soilTemp}</div>
      </div>
      <div class="crop-tips">${crop.tips}</div>`;
    cropsGrid.appendChild(cropCard);
  });
}

// Tasks
function initializeTasks() {
  document.getElementById('task-filter').addEventListener('change', renderTasks);
  renderTasks();
}

function renderTasks() {
  const statusFilter = document.getElementById('task-filter').value;
  const filteredTasks = appData.tasks.filter(task => !statusFilter || task.status === statusFilter);
  const tasksList = document.getElementById('tasks-list');
  tasksList.innerHTML = '';

  filteredTasks.forEach(task => {
    const taskTitle = translations[currentLanguage][task.titleKey] || task.titleKey;
    const taskElement = document.createElement('div');
    taskElement.className = 'task-item';
    taskElement.innerHTML = `
      <div class="task-content">
        <div class="task-title">${taskTitle}</div>
        <div class="task-meta"><span>${formatDate(task.date)}</span><span>${task.category}</span></div>
      </div>
      <div class="task-actions">
        <span class="task-priority ${task.priority.toLowerCase()}">${task.priority}</span>
        <span class="task-status ${task.status.toLowerCase()}">${task.status}</span>
        <button class="btn btn--sm ${task.status === 'Pending' ? 'btn--primary' : 'btn--secondary'}" onclick="toggleTaskStatus(${task.id})">
          ${task.status === 'Pending' ? translations[currentLanguage].complete : translations[currentLanguage].reopen}
        </button>
      </div>`;
    tasksList.appendChild(taskElement);
  });
}

function toggleTaskStatus(taskId) {
  const task = appData.tasks.find(t => t.id === taskId);
  if (task) {
    task.status = task.status === 'Pending' ? 'Completed' : 'Pending';
    renderTasks();
    if (currentSection === 'dashboard') updateDashboard();
  }
}

// Weather
function initializeWeather() { updateWeather(); }

function updateWeather() {
  const current = appData.weather.current;
  // --- UPDATED TO CELSIUS ---
  document.getElementById('current-weather-temp').textContent = current.temp + '°C';
  document.getElementById('current-weather-condition').textContent = current.condition;
  document.getElementById('current-humidity').textContent = current.humidity + '%';
  document.getElementById('current-wind').textContent = current.wind + ' mph';

  const forecastGrid = document.getElementById('forecast-grid');
  forecastGrid.innerHTML = '';

  appData.weather.forecast.forEach(forecast => {
    const forecastItem = document.createElement('div');
    forecastItem.className = 'forecast-item';
    // --- UPDATED TO CELSIUS ---
    forecastItem.innerHTML = `
      <div class="forecast-day">${forecast.day}</div>
      <div class="forecast-temps">
        <span class="forecast-high">${forecast.high}°</span>
        <span class="forecast-low">${forecast.low}°</span>
      </div>
      <div class="forecast-condition">${forecast.condition}</div>`;
    forecastGrid.appendChild(forecastItem);
  });
}


// Rotation
function initializeRotation() {
  const cropSlots = document.querySelectorAll('.crop-slot');
  cropSlots.forEach(slot => {
    slot.addEventListener('click', (e) => {
      const slotId = e.target.id;
      const [field, year] = slotId.split('-');
      selectedField = field.replace('field', '');
      selectedYear = year;

      document.getElementById('assignment-field-info').textContent = `Field ${selectedField} - ${year}`;
      renderCropSelection();
      showModal('crop-assignment-modal');
    });
  });
}
function updateRotationText() {
    document.querySelectorAll('.crop-slot').forEach(slot => {
        if (!slot.classList.contains('assigned')) {
            slot.textContent = translations[currentLanguage].assignCropPrompt;
        }
    });
}
function renderCropSelection() {
  const grid = document.getElementById('crop-selection-grid');
  grid.innerHTML = '';
  appData.crops.forEach(crop => {
    const cropName = translations[currentLanguage][crop.nameKey] || crop.nameKey;
    const cropOption = document.createElement('div');
    cropOption.className = 'crop-option';
    cropOption.textContent = cropName;
    cropOption.addEventListener('click', () => assignCropToField(crop.nameKey, cropName));
    grid.appendChild(cropOption);
  });
}
function assignCropToField(cropKey, cropDisplayName) {
  if (selectedField && selectedYear) {
    const slotId = `field${selectedField}-${selectedYear}`;
    const slot = document.getElementById(slotId);
    slot.textContent = cropDisplayName;
    slot.classList.add('assigned');
    if (!appData.rotation[`field${selectedField}`]) appData.rotation[`field${selectedField}`] = {};
    appData.rotation[`field${selectedField}`][selectedYear] = cropKey;
    hideModal('crop-assignment-modal');
  }
}

// Reports
function initializeReports() { updateReports(); }
function updateReports() { /* Chart logic remains unchanged */ }

// Modals
function initializeModals() {
  document.getElementById('add-event-btn').addEventListener('click', () => showModal('event-modal'));
  document.getElementById('close-event-modal').addEventListener('click', () => hideModal('event-modal'));
  document.getElementById('cancel-event').addEventListener('click', () => hideModal('event-modal'));
  document.getElementById('event-form').addEventListener('submit', handleEventSubmit);

  document.getElementById('add-crop-btn').addEventListener('click', () => showModal('crop-modal'));
  document.getElementById('close-crop-modal').addEventListener('click', () => hideModal('crop-modal'));
  document.getElementById('cancel-crop').addEventListener('click', () => hideModal('crop-modal'));
  document.getElementById('crop-form').addEventListener('submit', handleCropSubmit);

  document.getElementById('add-task-btn').addEventListener('click', () => showModal('task-modal'));
  document.getElementById('close-task-modal').addEventListener('click', () => hideModal('task-modal'));
  document.getElementById('cancel-task').addEventListener('click', () => hideModal('task-modal'));
  document.getElementById('task-form').addEventListener('submit', handleTaskSubmit);

  document.getElementById('close-assignment-modal').addEventListener('click', () => hideModal('crop-assignment-modal'));

  document.querySelectorAll('.modal').forEach(modal => {
    modal.addEventListener('click', (e) => { if (e.target === modal) hideModal(modal.id); });
  });
}

function showModal(modalId) { document.getElementById(modalId).classList.remove('hidden'); }
function hideModal(modalId) { document.getElementById(modalId).classList.add('hidden'); }

// --- These handler functions remain the same as they don't deal with temperature ---
function handleEventSubmit(e) {
    e.preventDefault();
    const newEvent = {
        id: appData.nextId.event++,
        title: document.getElementById('event-title').value,
        date: document.getElementById('event-date').value,
        type: document.getElementById('event-type').value
    };
    appData.events.push(newEvent);
    document.getElementById('event-form').reset();
    hideModal('event-modal');
    if (currentSection === 'calendar') renderCalendar();
    if (currentSection === 'dashboard') updateDashboard();
}

function handleCropSubmit(e) {
    e.preventDefault();
    const newCrop = {
        id: appData.nextId.crop++,
        name: document.getElementById('crop-name').value,
        family: document.getElementById('crop-family').value,
        daysToMaturity: parseInt(document.getElementById('crop-maturity').value),
        plantingDepth: document.getElementById('crop-depth').value,
        spacing: document.getElementById('crop-spacing').value,
        soilTemp: "Unknown",
        tips: "Custom crop - add your own growing tips",
        plantingMonths: []
    };
    appData.crops.push(newCrop);
    document.getElementById('crop-form').reset();
    hideModal('crop-modal');
    if (currentSection === 'crops') renderCrops();
    if (currentSection === 'dashboard') updateDashboard();
}

function handleTaskSubmit(e) {
    e.preventDefault();
    const newTask = {
        id: appData.nextId.task++,
        title: document.getElementById('task-title').value,
        category: document.getElementById('task-category').value,
        date: document.getElementById('task-date').value,
        priority: document.getElementById('task-priority').value,
        status: 'Pending'
    };
    appData.tasks.push(newTask);
    document.getElementById('task-form').reset();
    hideModal('task-modal');
    if (currentSection === 'tasks') renderTasks();
    if (currentSection === 'dashboard') updateDashboard();
}


// Utility Functions
function formatDate(dateString) {
  const date = new Date(dateString);
  const langCode = currentLanguage === 'mr' ? 'mr-IN' : 'en-US';
  return date.toLocaleDateString(langCode, { year: 'numeric', month: 'short', day: 'numeric' });
}