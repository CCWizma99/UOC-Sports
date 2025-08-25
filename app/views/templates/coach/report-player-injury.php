
  <div class="container">
    <div class="header">
      <h1 class="title">Report Injury & Suggest Substitute</h1>
    </div>

    <!-- Injury Form -->
    <form id="injuryForm" class="form-card">
      <div class="form-group">
        <label for="playerId">Injured Player ID</label>
        <input type="text" id="playerId" placeholder="Enter Injured Player ID" required>
      </div>

      <div class="form-group">
        <label for="injuryDate">Date</label>
        <input type="date" id="injuryDate" required>
      </div>

      <div class="form-group">
        <label for="injuryType">Injury Type</label>
        <input type="text" id="injuryType" placeholder="Enter Injury Type" required>
      </div>

      <div class="form-group">
        <label for="injuryPhoto">Upload Injury Photo</label>
        <input type="file" id="injuryPhoto" accept="image/*">
      </div>

      <div class="form-group toggle">
        <label for="toggleSubstitute">Suggest a Substitute?</label>
        <input type="checkbox" id="toggleSubstitute">
      </div>

      <div class="form-group" id="substituteField" style="display: none;">
        <label for="substituteId">Substitute Player ID</label>
        <input type="text" id="substituteId" placeholder="Enter Player ID or Name">
      </div>

      <button type="submit" class="submit-btn">Submit Report</button>
    </form>

    <!-- Recent Injury Reports -->
    <h3 class="section-title">Recent Injury Reports</h3>
    <div class="injury-table">
      <div class="table-header">
        <div>Injured Player ID</div>
        <div>Date</div>
        <div>Injury Type</div>
        <div>Substitute Player ID</div>
      </div>
      <div class="table-row">
        <div>2023/CS/102</div>
        <div>2024-07-20</div>
        <div>severe</div>
        <div>2023/CS/284</div>
      </div>
      <div class="table-row">
        <div>2023/CS/011</div>
        <div>2024-07-28</div>
        <div>normal</div>
        <div>2023/CS/201</div>
      </div>
      <div class="table-row">
        <div>2023/CS/001</div>
        <div>2024-07-12</div>
        <div>minor</div>
        <div>2023/CS/123</div>
      </div>
    </div>
  </div>

  
