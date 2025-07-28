function calculateYield() {
  const frameInput = document.getElementById("frameCount");
  const avgInput = document.getElementById("avgPerFrame");
  const resultDisplay = document.getElementById("resultDisplay");

  const frameCount = parseFloat(frameInput.value);
  const avgPerFrame = parseFloat(avgInput.value);

  if (isNaN(frameCount) || isNaN(avgPerFrame)) {
    resultDisplay.innerHTML = "⚠️ Please enter valid numbers for both fields.";
    return;
  }

  const totalYield = frameCount * avgPerFrame;

  resultDisplay.innerHTML = `
    ✅ Estimated Honey Yield: <strong>${totalYield.toFixed(2)} kg</strong>
  `;
}
function getWeather() {
  const location = document.getElementById("locationInput").value.trim();
  const loading = document.getElementById("loading");
  const resultBox = document.getElementById("resultBox");

  if (!location) {
    loading.textContent = "❗ Please enter a location.";
    resultBox.style.display = "none";
    return;
  }

  loading.textContent = "Looking up location...";
  resultBox.style.display = "none";

  const geoUrl = `https://geocoding-api.open-meteo.com/v1/search?name=${encodeURIComponent(location)}&count=1`;

  fetch(geoUrl)
    .then(res => res.json())
    .then(geo => {
      if (!geo.results || geo.results.length === 0) {
        loading.textContent = "❌ Location not found.";
        return;
      }

      const { latitude, longitude, timezone } = geo.results[0];
      loading.textContent = "Getting weather details...";

      const weatherUrl = `https://api.open-meteo.com/v1/forecast?latitude=${latitude}&longitude=${longitude}&hourly=temperature_2m,relative_humidity_2m,dew_point_2m,apparent_temperature,wind_speed_10m,precipitation&timezone=${timezone}`;

      fetch(weatherUrl)
        .then(res => res.json())
        .then(data => {
          const i = 0; // first hour
          document.getElementById("resultTime").textContent = new Date(data.hourly.time[i]).toLocaleString();
          document.getElementById("temp").textContent = data.hourly.temperature_2m[i]?.toFixed(1) ?? "-";
          document.getElementById("humidity").textContent = data.hourly.relative_humidity_2m[i]?.toFixed(0) ?? "-";
          document.getElementById("dew").textContent = data.hourly.dew_point_2m[i]?.toFixed(1) ?? "-";
          document.getElementById("apparent").textContent = data.hourly.apparent_temperature[i]?.toFixed(1) ?? "-";
          document.getElementById("wind").textContent = data.hourly.wind_speed_10m[i]?.toFixed(1) ?? "-";
          document.getElementById("rain").textContent = data.hourly.precipitation[i]?.toFixed(1) ?? "-";

          resultBox.style.display = "block";
          loading.textContent = "";
        })
        .catch(() => {
          loading.textContent = "❌ Weather fetch failed.";
        });
    })
    .catch(() => {
      loading.textContent = "❌ Location lookup failed.";
    });
}


