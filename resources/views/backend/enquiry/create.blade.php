@extends('layouts.admin')

@section('content')
<div class="col-lg-12 d-flex align-items-stretch">
  <div class="card w-100">
    <div class="card-body pb-0">
      <h4 class="card-title">Enquiry Form</h4>
      <p class="card-subtitle mb-3">Fill out the form to create a new enquiry.</p>
    </div>
    
    <form action="{{ route('backend.enquiry.store') }}" method="POST" id="enquiryForm">
      @csrf
      
      <!-- Personal Information Section -->
      <div class="card-body border-top">
        <h5 class="mb-3">Personal Information</h5>
        <div class="row">
          <div class="col-sm-12 col-md-6">
            <div class="mb-3">
              <label for="uname" class="form-label">Student Name</label>
              <input type="text" class="form-control" id="uname" name="uname" placeholder="Enter your name" required>
            </div>
          </div>
          <div class="col-sm-12 col-md-6">
            <div class="mb-3">
              <label for="email" class="form-label">Email ID</label>
              <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-12 col-md-6">
            <div class="mb-3">
              <label for="contact" class="form-label">Contact Number</label>
              <input type="tel" class="form-control" id="contact" name="contact" placeholder="Enter your contact number" required>
            </div>
          </div>
          <div class="col-sm-12 col-md-6">
            <div class="mb-3">
              <label for="guardians" class="form-label">Guardian's Name</label>
              <input type="text" class="form-control" id="guardians" name="guardians" placeholder="Enter guardian's name">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-12 col-md-6">
            <div class="mb-3">
              <label for="contacts" class="form-label">Guardian's Number</label>
              <input type="tel" class="form-control" id="contacts" name="contacts" placeholder="Enter guardian's number">
            </div>
          </div>
          <div class="col-sm-12 col-md-6">
            <div class="mb-3">
              <label for="country" class="form-label">Country</label>
              <select class="form-select" id="country" name="country">
                <option value="" disabled selected>Select Country</option>
                <option value="USA">USA</option>
                <option value="UK">UK</option>
                <option value="Australia">Australia</option>
                <option value="Canada">Canada</option>
              </select>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-12 col-md-6">
            <div class="mb-3">
              <label for="education" class="form-label">Education</label>
              <select class="form-select" id="education" name="education">
                <option value="" disabled selected>Select Education</option>
                <option value="Intermediate">Intermediate</option>
                <option value="Bachelor">Bachelor</option>
                <option value="Master">Master</option>
              </select>
            </div>
          </div>
          <div class="col-sm-12 col-md-6">
            <div class="mb-3">
              <label for="about" class="form-label">Know About Us</label>
              <select class="form-select" id="about" name="about">
                <option value="" disabled selected>Select About</option>
                <option value="Facebook">Facebook</option>
                <option value="Tiktok">Tiktok</option>
                <option value="Exhibition">Exhibition</option>
                <option value="TV/FM">TV/FM</option>
                <option value="Instagram">Instagram</option>
                <option value="Seminar">Seminar</option>
                <option value="Friends">Friends</option>
                <option value="NewsPaper">NewsPaper</option>
              </select>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Test Scores Section -->
      <div class="card-body border-top">
        <h5 class="mb-3">Test Scores</h5>
        <div class="row">
          <div class="col-sm-12 col-md-4">
            <div class="mb-3">
              <label for="ielts" class="form-label">IELTS</label>
              <input type="text" class="form-control" id="ielts" name="ielts" placeholder="Enter your IELTS score">
            </div>
          </div>
          <div class="col-sm-12 col-md-4">
            <div class="mb-3">
              <label for="toefl" class="form-label">TOEFL</label>
              <input type="text" class="form-control" id="toefl" name="toefl" placeholder="Enter your TOEFL score">
            </div>
          </div>
          <div class="col-sm-12 col-md-4">
            <div class="mb-3">
              <label for="ellt" class="form-label">ELLT</label>
              <input type="text" class="form-control" id="ellt" name="ellt" placeholder="Enter your ELLT score">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-12 col-md-4">
            <div class="mb-3">
              <label for="pte" class="form-label">PTE</label>
              <input type="text" class="form-control" id="pte" name="pte" placeholder="Enter your PTE score">
            </div>
          </div>
          <div class="col-sm-12 col-md-4">
            <div class="mb-3">
              <label for="sat" class="form-label">SAT</label>
              <input type="text" class="form-control" id="sat" name="sat" placeholder="Enter your SAT score">
            </div>
          </div>
          <div class="col-sm-12 col-md-4">
            <div class="mb-3">
              <label for="gre" class="form-label">GRE</label>
              <input type="text" class="form-control" id="gre" name="gre" placeholder="Enter your GRE score">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-12 col-md-6">
            <div class="mb-3">
              <label for="gmat" class="form-label">GMAT</label>
              <input type="text" class="form-control" id="gmat" name="gmat" placeholder="Enter your GMAT score">
            </div>
          </div>
          <div class="col-sm-12 col-md-6">
            <div class="mb-3">
              <label for="other" class="form-label">Other Test Score</label>
              <input type="text" class="form-control" id="other" name="other" placeholder="Enter other test score">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-12 col-md-6">
            <div class="mb-3">
              <label for="counselor" class="form-label">Counselor</label>
              <select class="form-select" id="counselor" name="counselor">
                <option value="" disabled selected>Select Counselor Name</option>
                @foreach($names as $name)
                  <option value="{{ $name->id }}">{{ $name->name }}</option>
                @endforeach
                <option value="Ram">Ram</option>
                <option value="Ram">Ichchya</option>
                <option value="Roshan">Roshan</option>
                <option value="Roshan">Prasamsha</option>
              </select>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <div class="mb-3">
              <label for="feedback" class="form-label">Feedback</label>
              <textarea class="form-control" id="feedback" name="feedback" rows="3" placeholder="Enter your feedback"></textarea>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Education History Section -->
      <div class="card-body border-top">
        <h5 class="mb-3">Education History</h5>
        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th>Level</th>
                <th>Institution Name</th>
                <th>Grade/Percentage</th>
                <th>Completion Year</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>SEE/SLC</td>
                <td><input type="text" class="form-control" name="institution1" placeholder="Enter institution name"></td>
                <td><input type="text" class="form-control" name="grade1" placeholder="Enter grade/percentage"></td>
                <td><input type="text" class="form-control" name="year1" placeholder="Enter completion year"></td>
              </tr>
              <tr>
                <td>+2</td>
                <td><input type="text" class="form-control" name="institution2" placeholder="Enter institution name"></td>
                <td><input type="text" class="form-control" name="grade2" placeholder="Enter grade/percentage"></td>
                <td><input type="text" class="form-control" name="year2" placeholder="Enter completion year"></td>
              </tr>
              <tr>
                <td>Bachelor</td>
                <td><input type="text" class="form-control" name="institution3" placeholder="Enter institution name"></td>
                <td><input type="text" class="form-control" name="grade3" placeholder="Enter grade/percentage"></td>
                <td><input type="text" class="form-control" name="year3" placeholder="Enter completion year"></td>
              </tr>
              <tr>
                <td>Master</td>
                <td><input type="text" class="form-control" name="institution4" placeholder="Enter institution name"></td>
                <td><input type="text" class="form-control" name="grade4" placeholder="Enter grade/percentage"></td>
                <td><input type="text" class="form-control" name="year4" placeholder="Enter completion year"></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      
      <!-- Form Actions -->
      <div class="p-3 border-top">
        <div class="text-end">
          <button type="submit" class="btn btn-primary">
            Submit Form
          </button>
          <button type="reset" class="btn bg-danger-subtle text-danger ms-6 px-4">
            Cancel
          </button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Add popup HTML -->
<div class="overlay" id="overlay"></div>
<div class="popup" id="successPopup">
  <div class="popup-content">
    <h2>Success!</h2>
    <p>Your enquiry has been submitted successfully.</p>
    <button class="popup-btn" onclick="closePopup()">OK</button>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const form = document.getElementById('enquiryForm');
  
  form.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    // Get all input and textarea elements
    const inputs = form.querySelectorAll('input:not([required]), textarea:not([required]), select:not([required])');
    
    // Set empty optional fields to N/A
    inputs.forEach(input => {
      if (!input.value.trim()) {
        input.value = 'N/A';
      }
    });
    
    try {
      // Submit the form using fetch
      const response = await fetch(form.action, {
        method: 'POST',
        body: new FormData(form)
      });
      
      if (response.ok) {
        // Show success popup
        showPopup();
        // Reset form after successful submission
        form.reset();
      } else {
        alert('Something went wrong. Please try again.');
      }
    } catch (error) {
      alert('An error occurred. Please try again.');
    }
  });
});

function showPopup() {
  document.getElementById('overlay').style.display = 'block';
  document.getElementById('successPopup').style.display = 'block';
}

function closePopup() {
  document.getElementById('overlay').style.display = 'none';
  document.getElementById('successPopup').style.display = 'none';
}
</script>

<style>
.overlay {
  display: none;
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  z-index: 1000;
}

.popup {
  display: none;
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background-color: white;
  padding: 20px;
  border-radius: 5px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
  z-index: 1001;
}

.popup-content {
  text-align: center;
}

.popup-btn {
  margin-top: 15px;
  padding: 8px 20px;
  background-color: #007bff;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.popup-btn:hover {
  background-color: #0056b3;
}
</style>
@endsection