const form = document.getElementById('registrationForm');
const auid = document.getElementById('auid');
const lbname = document.getElementById('lbname');
const lblastname = document.getElementById('lblastname');
const personId = document.getElementById('person_ID_librarian');
const email = document.getElementById('email');
const gender = document.getElementById('lbgender');
const dob = document.getElementById('lb_dob');
const phone = document.getElementById('lbphone');
const password = document.getElementById('password');
const confirmPassword = document.getElementById('confirmPassword');

form.addEventListener('submit', e => {
  // Stop the form from submitting to the server
  e.preventDefault();
  // If validation passes, then the form will submit
  if (validateInputs()) {
    form.submit();
  }
});

const setError = (element, message) => {
  const formGroup = element.parentElement;
  const errorDisplay = formGroup.querySelector('.error-message');
  errorDisplay.innerText = message;
  // Add the 'error' class to the parent .form-group
  formGroup.classList.add('error');
  formGroup.classList.remove('success');
}

const setSuccess = element => {
  const formGroup = element.parentElement;
  const errorDisplay = formGroup.querySelector('.error-message');
  errorDisplay.innerText = '';
  // Add the 'success' class to the parent .form-group
  formGroup.classList.add('success');
  formGroup.classList.remove('error');
};

// ADDED: ใช้ regex อีเมลที่เรียบง่ายและเชื่อถือได้สำหรับ client-side
const isValidEmail = email => {
  const re = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
  return re.test(String(email).toLowerCase());
}

// ADDED: ฟังก์ชัน validate ทีละฟิลด์ (ใช้ซ้ำได้ทั้งตอนพิมพ์/เปลี่ยนค่า และตอน submit)
const validateField = (el) => {
  const id = el.id;
  const value = (el.value || '').trim();
  switch (id) {
    case 'auid':
      if (value.length < 5) {
        setError(el, 'Username ต้องมีอย่างน้อย 5 ตัวอักษร');
        return false;
      }
      setSuccess(el);
      return true;

    case 'lbname':
      if (value === '') {
        setError(el, 'กรุณากรอกชื่อผู้ใช้');
        return false;
      }
      setSuccess(el);
      return true;

    case 'lblastname':
      if (value === '') {
        setError(el, 'กรุณากรอกนามสกุล');
        return false;
      }
      setSuccess(el);
      return true;

    case 'person_ID_librarian':
      if (!/^\d{13}$/.test(value)) { // FIX: Test the value, not the element
        setError(el, 'รหัสบัตรประชาชนต้องเป็นตัวเลข 13 หลัก');
        return false;
      }
      setSuccess(el);
      return true;

    case 'email':
      if (value === '') {
        setError(el, 'กรุณากรอกอีเมล');
        return false;
      } else if (!isValidEmail(value)) {
        setError(el, 'รูปแบบอีเมลไม่ถูกต้อง');
        return false;
      }
      setSuccess(el);
      return true;

    case 'lbgender':
      if (value === '') {
        setError(el, 'กรุณาเลือกเพศ');
        return false;
      }
      setSuccess(el);
      return true;

    case 'lb_dob':
      if (value === '') {
        setError(el, 'กรุณาเลือกวันเกิด');
        return false;
      }
      setSuccess(el);
      return true;

    case 'lbphone':
      if (!/^\d{9,10}$/.test(value)) { // FIX: Test the value, not the element
        setError(el, 'เบอร์โทรศัพท์ต้องเป็นตัวเลข 9-10 หลัก');
        return false;
      }
      setSuccess(el);
      return true;

    case 'password':
      if (value === '') {
        setError(el, 'กรุณากรอกรหัสผ่าน');
        return false;
      } else if (value.length < 8) {
        setError(el, 'รหัสผ่านต้องมีอย่างน้อย 8 ตัวอักษร');
        return false;
      }
      setSuccess(el);
      // ADDED: หากมีการเปลี่ยนรหัส ให้ re-validate ยืนยันรหัสผ่านด้วย
      if (confirmPassword.value.trim() !== '') validateField(confirmPassword);
      return true;

    case 'confirmPassword':
      if (value === '') {
        setError(el, 'กรุณากรอกยืนยันรหัสผ่าน'); // FIX: Use correct variable 'confirmPassword'
        return false;
      } else if (value !== password.value.trim()) {
        setError(el, "รหัสผ่านไม่ตรงกัน"); // FIX: Use correct variable 'confirmPassword'
        return false;
      }
      setSuccess(el);
      return true;

    default:
      return true;
  }
};

const validateInputs = () => {
  const usernameValue = auid.value.trim();
  const emailValue = email.value.trim();
  const fullnameValue = lbname.value.trim();
  const lastnameValue = lblastname.value.trim();
  const personIdValue = personId.value.trim();
  const dobValue = dob.value.trim();
  const phoneValue = phone.value.trim();
  const genderValue = gender.value;
  const passwordValue = password.value.trim();
  const password2Value = confirmPassword.value.trim();

  let isValid = true; // Flag to track overall validation status

  if (usernameValue.length < 5) { // FIX: Check length of the value
    setError(auid, 'Username ต้องมีอย่างน้อย 5 ตัวอักษร');
    isValid = false;
  } else {
    setSuccess(auid);
  }

  if (fullnameValue === '') {
    setError(lbname, 'กรุณากรอกชื่อผู้ใช้');
    isValid = false;
  } else {
    setSuccess(lbname);
  }

  if (lastnameValue === '') {
    setError(lblastname, 'กรุณากรอกนามสกุล');
    isValid = false;
  } else {
    setSuccess(lblastname);
  }

  if (!/^\d{13}$/.test(personIdValue)) { // FIX: Test the value, not the element
    setError(personId, 'รหัสบัตรประชาชนต้องเป็นตัวเลข 13 หลัก');
    isValid = false;
  } else {
    setSuccess(personId);
  }

  if (emailValue === '') {
    setError(email, 'กรุณากรอกอีเมล');
    isValid = false;
  } else if (!isValidEmail(emailValue)) {
    setError(email, 'รูปแบบอีเมลไม่ถูกต้อง');
    isValid = false;
  } else {
    setSuccess(email);
  }

  if (genderValue === '') {
    setError(gender, 'กรุณาเลือกเพศ');
    isValid = false;
  } else {
    setSuccess(gender);
  }

  if (dobValue === '') {
    setError(dob, 'กรุณาเลือกวันเกิด');
    isValid = false;
  } else {
    setSuccess(dob);
  }

  if (!/^\d{9,10}$/.test(phoneValue)) { // FIX: Test the value, not the element
    setError(phone, 'เบอร์โทรศัพท์ต้องเป็นตัวเลข 9-10 หลัก');
    isValid = false;
  } else {
    setSuccess(phone);
  }

  if (passwordValue === '') {
    setError(password, 'กรุณากรอกรหัสผ่าน');
    isValid = false;
  } else if (passwordValue.length < 8) {
    setError(password, 'รหัสผ่านต้องมีอย่างน้อย 8 ตัวอักษร');
    isValid = false;
  } else {
    setSuccess(password);
  }

  if (password2Value === '') {
    setError(confirmPassword, 'กรุณากรอกยืนยันรหัสผ่าน'); // FIX: Use correct variable 'confirmPassword'
    isValid = false;
  } else if (password2Value !== passwordValue) {
    setError(confirmPassword, "รหัสผ่านไม่ตรงกัน"); // FIX: Use correct variable 'confirmPassword'
    isValid = false;
  } else {
    setSuccess(confirmPassword);
  }

  return isValid; // Return the final validation status
};

// ADDED: ผูก live validation ให้แสดงกรอบแดง/เขียวและข้อความทันที
[
  auid, lbname, lblastname, personId, email, dob, phone, password, confirmPassword
].forEach(el => {
  el.addEventListener('input', () => validateField(el));
  el.addEventListener('blur', () => validateField(el));
});
gender.addEventListener('change', () => validateField(gender));