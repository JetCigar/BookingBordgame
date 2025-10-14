const container = document.querySelector('.container');
const registerBtn = document.querySelector('.register-btn');
const loginBtn = document.querySelector('.login-btn');

registerBtn.addEventListener('click' , () =>{
    container.classList.add('active');

});
loginBtn.addEventListener('click' , () =>{
    container.classList.remove('active');
});
function togglePassword() {
  const pwd = document.getElementById('password');
  pwd.type = pwd.type === 'password' ? 'text' : 'password';
}