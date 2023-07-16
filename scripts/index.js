async function getWeather() {
  let cityInput = document.getElementById('city-input').value || "Aberdeen";
  const data = await getWeatherData(cityInput);
  if (data.cod == 404) {
    const errorDom = document.querySelector("#error")
    errorDom.innerHTML = `${cityInput} City not found`
    errorDom.className = "text-red-500 mb-4 rounded-lg bg-red-100 px-6 py-5 text-base text-danger-700"
    return;
  }
  let locationElement = document.getElementById('location');
  let temperatureElement = document.getElementById('temperature');
  let descriptionElement = document.getElementById('description');
  let pressureElement = document.getElementById('pressure');
  let windSpeedElement = document.getElementById('windSpeed');
  let humidityElement = document.getElementById('humidity');
  currentData(data.timezone);

  locationElement.innerHTML = 'City: ' + cityInput;
  temperatureElement.innerHTML =
    `
    <span>
    ${data.main.temp}℃ 
    <img src="http://openweathermap.org/img/w/${data.weather[0].icon}.png" alt="Weather Icon">
    
    </span>
    `;
  descriptionElement.innerHTML = `Description: ${data.weather[0].main}`;
  pressureElement.innerHTML = `${data.main.pressure}`;
  windSpeedElement.innerHTML = `${data.wind.speed} m/s`;
  humidityElement.innerHTML = `${data.main.humidity} %`;
  document.querySelector("#error").innerHTML = '';
}

function toggleDarkMode() {
  let bodyElement = document.body;
  bodyElement.classList.toggle('dark-mode');
}


const getWeatherData = async (cityName) => {
  const apiKEY = "23d7b189189528d5ade1c10729887b94"
  const apiUrl = `https://api.openweathermap.org/data/2.5/weather?q=${cityName}&units=metric&appid=${apiKEY}`;
  const data = await fetch(`${apiUrl}`);
  const weatherData = await data.json();
  return weatherData;
}

function currentData(timezone) {
  let currentDate = new Date();
  let utcOffset = currentDate.getTimezoneOffset() * 60 * 1000;
  let cityOffset = timezone * 1000;
  let currentTimestamp = currentDate.getTime() + utcOffset + cityOffset;
  let localDate = new Date(currentTimestamp);
  let options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: 'numeric', minute: 'numeric' };
  let formattedDate = localDate.toLocaleDateString(undefined, options);

  document.getElementById('current-day-and-date').textContent = formattedDate;
}





document.querySelector("#city-input").addEventListener("keydown", (event) => {
  if (event.key === "Enter") {
    getWeather();
  }
});

getWeather();

