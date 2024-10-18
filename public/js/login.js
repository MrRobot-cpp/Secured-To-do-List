
const contain=document.querySelector(".container"),
signUp=document.querySelector(".signup-link"),
login=document.querySelector(".login-link");


signUp.addEventListener('click',()=>{
  
contain.classList.add("active");

});
login.addEventListener('click',()=>{
  
    contain.classList.remove("active");
    
});


