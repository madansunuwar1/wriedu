<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enquiry Form</title>

    @include('backend.script.session')


    @include('backend.script.alert')

    
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Georgia', serif;
            background-color: #f5f5f0;
            min-height: 100vh;
            padding: 40px 20px;
            color: #2c3e50;
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
        }

        form {
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            padding: 40px;
        }

        h1 {
            font-size: 36px;
            color: #2c3e50;
            text-align: center;
            margin-bottom: 40px;
            position: relative;
            padding-bottom: 15px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        h1::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 120px;
            height: 3px;
            background-color: #2e7d32;
        }

        .section-title {
            font-size: 24px;
            color: #2e7d32;
            margin: 35px 0 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #81c784;
            font-weight: 600;
            letter-spacing: 0.3px;
        }

        .form-row {
            display: flex;
            gap: 25px;
            margin-bottom: 25px;
        }

        .form-group {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            display: block;
            font-size: 16px;
            color: #2e7d32;
            margin-bottom: 10px;
            font-weight: 600;
            letter-spacing: 0.2px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 14px;
            font-size: 15px;
            color: #2c3e50;
            border-radius: 6px;
            border: 1.5px solid #81c784;
            transition: all 0.3s ease;
            font-family: 'Georgia', serif;
            background-color: #fcfcfc;
        }

        .form-group textarea {
            width:490px;
            min-height: 120px;
            
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #2e7d32;
            box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.1);
        }

        .education-table {
            width: 100%;
            border-collapse: collapse;
            margin: 25px 0;
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .education-table th,
        .education-table td {
            border: 1px solid #e8f5e9;
            padding: 15px 20px;
            text-align: left;
            font-size: 15px;
        }

        .education-table th {
            background-color: #2e7d32;
            color: #ffffff;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 14px;
            letter-spacing: 0.8px;
        }

        .education-table tr:hover {
            background-color: #f1f8e9;
        }

        .education-table td input {
            width: 100%;
            padding: 12px 15px;
            border: 1.5px solid #81c784;
            border-radius: 4px;
            font-size: 15px;
            font-family: 'Georgia', serif;
        }

        .education-table td input:focus {
            outline: none;
            border-color: #2e7d32;
            box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.1);
        }

        .btn-submit {
            background-color: #2e7d32;
            color: #fff;
            border: none;
            padding: 16px 35px;
            font-size: 18px;
            border-radius: 6px;
            cursor: pointer;
            display: block;
            margin: 40px auto 0;
            min-width: 220px;
            transition: all 0.3s ease;
            font-weight: 600;
            letter-spacing: 0.5px;
            font-family: 'Georgia', serif;
        }

        .btn-submit:hover {
            background-color: #1b5e20;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(46, 125, 50, 0.3);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        @media (max-width: 768px) {
            .form-row {
                flex-direction: column;
                gap: 20px;
            }

            form {
                padding: 25px;
            }

            .education-table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }

            h1 {
                font-size: 28px;
            }

            .section-title {
                font-size: 22px;
            }
        }
    </style>
        
</head>
<body>
    <!-- Rest of the HTML remains unchanged -->
    <div class="container">
        <form action="{{ route('backend.enquiry.update', $enquiries->id) }}" method="POST">
        @csrf
        @method('PUT')
            <h1>Enquiry Form</h1>

            <!-- Personal Information Section -->
            <div class="section-title">Personal Information</div>
            <div class="form-row">
                <div class="form-group">
                    <label for="uname">Student Name</label>
                    <input type="text" id="uname" name="uname" placeholder="Enter your name" value="{{ old('uname', $enquiries->uname) }}" required>
                </div>
                <div class="form-group">
                    <label for="email">Email ID</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email" value="{{ old('email', $enquiries->email) }}" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="contact">Contact Number</label>
                    <input type="tel" id="contact" name="contact" placeholder="Enter your contact number" value="{{ old('contact', $enquiries->contact) }}"  required>
                </div>
                <div class="form-group">
                    <label for="guardians">Guardian's Name</label>
                    <input type="text" id="guardians" name="guardians" placeholder="Enter guardian's name" value="{{ old('guardians', $enquiries->guardians) }}"  required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="contacts">Guardian's Number</label>
                    <input type="tel" id="contacts" name="contacts" placeholder="Enter guardian's number" value="{{ old('contacts', $enquiries->contacts) }}"  required>
                </div>
                <div class="form-group">
                <label for="country">Country</label>
                    <select id="country" name="country" required>
                    <option value="" disabled selected>Select Country</option>
                    <option value="USA" {{ old('country', $enquiries->country ?? '') == 'USA' ? 'selected' : '' }}>USA</option>
                    <option value="UK" {{ old('country', $enquiries->country ?? '') == 'UK' ? 'selected' : '' }}>UK</option>
                    <option value="Australia" {{ old('country', $enquiries->country ?? '') == 'Australia' ? 'selected' : '' }}>Australia</option>
                    <option value="Canada" {{ old('country', $enquiries->country ?? '') == 'Canada' ? 'selected' : '' }}>Canada</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                <label for="education">Education</label>
                    <select id="education" name="education" required>
                        <option value="" disabled selected>Select Education</option>
                        <option value="Intermediate" {{ old('education', $enquiries->education ?? '') == 'Intermediate' ? 'selected' : '' }}>
                         Intermediate
                        </option>
                        <option value="Bachelor" {{ old('education', $enquiries->education ?? '') == 'Bachelor' ? 'selected' : '' }}>
                         Bachelor
                        </option>
                       <option value="Master" {{ old('education', $enquiries->education ?? '') == 'Master' ? 'selected' : '' }}>
                        Master
                      </option>
                    </select>
                </div>
                <div class="form-group">
                <label for="about">Know About Us</label>
                    <select id="about" name="about" required>
                        <option value="" disabled selected>Select About</option>
                        <option value="Facebook" {{ old('about', $enquiries->about ?? '') == 'Facebook' ? 'selected' : '' }}>Facebook</option>
                        <option value="Tiktok" {{ old('about', $enquiries->about ?? '') == 'Tiktok' ? 'selected' : '' }}>Tiktok</option>
                        <option value="Exhibition" {{ old('about', $enquiries->about ?? '') == 'Exhibition' ? 'selected' : '' }}>Exhibition</option>
                        <option value="TV/FM" {{ old('about', $enquiries->about ?? '') == 'TV/FM' ? 'selected' : '' }}>TV/FM</option>
                       <option value="Instagram" {{ old('about', $enquiries->about ?? '') == 'Instagram' ? 'selected' : '' }}>Instagram</option>
                       <option value="Seminar" {{ old('about', $enquiries->about ?? '') == 'Seminar' ? 'selected' : '' }}>Seminar</option>
                       <option value="Friends" {{ old('about', $enquiries->about ?? '') == 'Friends' ? 'selected' : '' }}>Friends</option>
                       <option value="NewsPaper" {{ old('about', $enquiries->about ?? '') == 'NewsPaper' ? 'selected' : '' }}>NewsPaper</option> 
                    </select>
                </div>
            </div>

            <!-- Test Scores Section -->
            <div class="section-title">Test Scores</div>
            <div class="form-row">
                <div class="form-group">
                    <label for="ielts">IELTS</label>
                    <input type="text" id="ielts" name="ielts" placeholder="Enter your IELTS score" value="{{ old('ielts', $enquiries->ielts) }}">
                </div>
                <div class="form-group">
                    <label for="toefl">TOEFL</label>
                    <input type="text" id="toefl" name="toefl" placeholder="Enter your TOEFL score" value="{{ old('toefl', $enquiries->toefl) }}">
                </div>

                <div class="form-group">
                    <label for="ellt">ELLT</label>
                    <input type="text" id="ellt" name="ellt" placeholder="Enter your ELLT score" value="{{ old('ellt', $enquiries->ellt) }}">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="pte">PTE</label>
                    <input type="text" id="pte" name="pte" placeholder="Enter your PTE score" value="{{ old('pte', $enquiries->pte) }}">
                </div>
                <div class="form-group">
                    <label for="sat">SAT</label>
                    <input type="text" id="sat" name="sat" placeholder="Enter your SAT score" value="{{ old('sat', $enquiries->sat) }}">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="gre">GRE</label>
                    <input type="text" id="gre" name="gre" placeholder="Enter your GRE score" value="{{ old('gre', $enquiries->gre) }}">
                </div>
                <div class="form-group">
                    <label for="gmat">GMAT</label>
                    <input type="text" id="gmat" name="gmat" placeholder="Enter your GMAT score" value="{{ old('gmat', $enquiries->gmat) }}">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="other">Other Test Score</label>
                    <input type="text" id="other" name="other" placeholder="Enter other test score" value="{{ old('other', $enquiries->other) }}">
                </div>
                <div class="form-group">
                    <label for="counselor">Counselor</label>
                    <input type="text" id="counselor" name="counselor" placeholder="Enter counselor" value="{{ old('counselor', $enquiries->counselor) }}">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                <label for="feedback">Feedback</label>
                <textarea id="feedback" name="feedback" placeholder="Enter your feedback">{{ old('feedback', $enquiries->feedback) }}</textarea>
                </div>
                
            </div>

            <!-- Education History Section -->
            <div class="section-title">Education History</div>
            <table class="education-table">
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
                        <td><input type="text" name="institution1" placeholder="Enter institution name" value="{{ old('institution1', $enquiries->institution1) }}"></td>
                        <td><input type="text" name="grade1" placeholder="Enter grade/percentage" value="{{ old('grade1', $enquiries->grade1) }}"></td>
                        <td><input type="text" name="year1" placeholder="Enter completion year" value="{{ old('year1', $enquiries->year1) }}"></td>
                    </tr>
                    <tr>
                        <td>+2</td>
                        <td><input type="text" name="institution2" placeholder="Enter institution name" value="{{ old('institution2', $enquiries->institution2) }}"></td>
                        <td><input type="text" name="grade2" placeholder="Enter grade/percentage" value="{{ old('grade2', $enquiries->grade2) }}"></td>
                        <td><input type="text" name="year2" placeholder="Enter completion year" value="{{ old('year2', $enquiries->year2) }}"></td>
                    </tr>
                    <tr>
                        <td>Bachelor</td>
                        <td><input type="text" name="institution3" placeholder="Enter institution name" value="{{ old('institution3', $enquiries->institution3) }}"></td>
                        <td><input type="text" name="grade3" placeholder="Enter grade/percentage" value="{{ old('grade3', $enquiries->grade3) }}"></td>
                        <td><input type="text" name="year3" placeholder="Enter completion year" value="{{ old('year3', $enquiries->year3) }}"></td>
                    </tr>
                    <tr>
                        <td>Master</td>
                        <td><input type="text" name="institution4" placeholder="Enter institution name" value="{{ old('institution4', $enquiries->institution4) }}"></td>
                        <td><input type="text" name="grade4" placeholder="Enter grade/percentage" value="{{ old('grade4', $enquiries->grade4) }}"></td>
                        <td><input type="text" name="year4" placeholder="Enter completion year" value="{{ old('year4', $enquiries->year4) }}"></td>
                    </tr>
                </tbody>
            </table>


            <button type="submit" class="btn-submit">Update</button>
        </form>
    </div>
</body>
</html>