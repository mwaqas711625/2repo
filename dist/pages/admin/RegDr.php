<?php 

require_once __DIR__ . '/../../header/header.php';

?>

<!-- html for page content -->

    <div class="row justify-content-center">
      <!-- Form ko center mein rakhne ke liye col-lg-9 use kiya hai -->
      <div class="col-12 col-lg-9">
        
        <div class="card shadow-sm border-0">
          <!-- Form Header -->
          <div class="card-header bg-primary text-white text-center py-3">
            <h3 class="mb-0">Doctor Registration Portal</h3>
          </div>

          <!-- Form Body (p-4 se charon taraf padding di hai) -->
          <div class="card-body p-4 p-md-5">
            <form action="#" method="POST" enctype="multipart/form-data">

              <!-- SECTION 1: Basic Information -->
              <h5 class="text-primary border-bottom pb-2 mb-3">1. Basic & Account Information</h5>
              <div class="row g-3 mb-4">
                <div class="col-12 col-md-6">
                  <label class="form-label">Full Name *</label>
                  <input type="text" class="form-control" placeholder="e.g. Dr. Ali Raza" required>
                </div>

                <div class="col-12 col-md-6">
                  <label class="form-label">Email Address *</label>
                  <input type="email" class="form-control" placeholder="doctor@example.com" required>
                </div>

                <div class="col-12 col-md-4">
                  <label class="form-label">Phone / WhatsApp *</label>
                  <input type="tel" class="form-control" placeholder="0300-1234567" required>
                </div>

                <div class="col-12 col-md-4">
                  <label class="form-label">Gender *</label>
                  <select class="form-select" required>
                    <option value="" selected disabled>Select Gender</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                  </select>
                </div>

                <div class="col-12 col-md-4">
                  <label class="form-label">Profile Photo</label>
                  <input type="file" class="form-control" accept="image/*">
                </div>

                <div class="col-12 col-md-6">
                  <label class="form-label">Password *</label>
                  <input type="password" class="form-control" placeholder="Create Password" required>
                </div>

                <div class="col-12 col-md-6">
                  <label class="form-label">Confirm Password *</label>
                  <input type="password" class="form-control" placeholder="Repeat Password" required>
                </div>
              </div>

              <!-- SECTION 2: Professional Credentials -->
              <h5 class="text-primary border-bottom pb-2 mb-3">2. Medical Credentials</h5>
              <div class="row g-3 mb-4">
                <div class="col-12 col-md-6">
                  <label class="form-label">PMDC Registration Number *</label>
                  <input type="text" class="form-control" placeholder="e.g. 12345-P" required>
                </div>

                <div class="col-12 col-md-6">
                  <label class="form-label">Specialization *</label>
                  <select class="form-select" required>
                    <option value="" selected disabled>Choose Specialization</option>
                    <option value="general">General Physician</option>
                    <option value="cardiologist">Cardiologist</option>
                    <option value="dermatologist">Dermatologist</option>
                    <option value="dentist">Dentist</option>
                    <option value="pediatrician">Pediatrician</option>
                  </select>
                </div>

                <div class="col-12 col-md-8">
                  <label class="form-label">Qualification / Degrees *</label>
                  <input type="text" class="form-control" placeholder="e.g. MBBS, FCPS" required>
                </div>

                <div class="col-12 col-md-4">
                  <label class="form-label">Experience (Years) *</label>
                  <input type="number" class="form-control" min="0" placeholder="e.g. 5" required>
                </div>
              </div>

              <!-- SECTION 3: Clinic & Fees -->
              <h5 class="text-primary border-bottom pb-2 mb-3">3. Clinic & Practice Details</h5>
              <div class="row g-3 mb-4">
                <div class="col-12 col-md-6">
                  <label class="form-label">Hospital / Clinic Name *</label>
                  <input type="text" class="form-control" placeholder="e.g. City Medical Center" required>
                </div>

                <div class="col-12 col-md-3">
                  <label class="form-label">City *</label>
                  <input type="text" class="form-control" placeholder="e.g. Lahore" required>
                </div>

                <div class="col-12 col-md-3">
                  <label class="form-label">Consultation Fee (PKR) *</label>
                  <input type="number" class="form-control" placeholder="e.g. 2000" required>
                </div>

                <div class="col-12">
                  <label class="form-label">Complete Clinic Address *</label>
                  <input type="text" class="form-control" placeholder="Street, Area, Main Landmark" required>
                </div>
              </div>

              <!-- SECTION 4: Documents Upload -->
              <h5 class="text-primary border-bottom pb-2 mb-3">4. Verification Documents</h5>
              <div class="row g-3 mb-4">
                <div class="col-12 col-md-6">
                  <label class="form-label">PMDC License Copy (PDF/JPG) *</label>
                  <input type="file" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                </div>

                <div class="col-12 col-md-6">
                  <label class="form-label">CNIC Front/Back Copy *</label>
                  <input type="file" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                </div>

                <div class="col-12">
                  <label class="form-label">Short Bio / About Me</label>
                  <textarea class="form-control" rows="3" placeholder="Apne tajurbay aur mahaarat ke baare mein mukhtasar likhein..."></textarea>
                </div>
              </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg">Register as Doctor</button>
                </div>

            </form>
          </div>
        </div>

      </div>
    </div>
<!-- html for page content end here -->


<?php
require_once __DIR__ . '/../../footer/footer.php';
?>