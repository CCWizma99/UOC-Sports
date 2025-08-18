// Event data
let currentEvent = null;
let teams = [];

// Handle event creation
document.getElementById('eventForm').addEventListener('submit', function(e) {
  e.preventDefault();
  const name = document.getElementById('eventName').value;
  const date = document.getElementById('eventDate').value;
  const sport = document.getElementById('eventSport').value;

  currentEvent = { name, date, sport };
  alert(`Event Created: ${name} (${sport}) on ${date}`);
  this.reset();
});

// Handle team registration
document.getElementById('teamForm').addEventListener('submit', function(e) {
  e.preventDefault();
  if (!currentEvent) {
    alert('Create an event first!');
    return;
  }

  const teamName = document.getElementById('teamName').value;
  const coachName = document.getElementById('coachName').value;
  const playersCount = document.getElementById('playersCount').value;

  const team = { teamName, coachName, playersCount };
  teams.push(team);
  updateTeamsList();
  this.reset();
});

// Update the teams list
function updateTeamsList() {
  const list = document.getElementById('teamsList');
  list.innerHTML = '';
  teams.forEach((team, index) => {
    const li = document.createElement('li');
    li.textContent = `${team.teamName} | Coach: ${team.coachName} | Players: ${team.playersCount}`;
    list.appendChild(li);
  });
}
