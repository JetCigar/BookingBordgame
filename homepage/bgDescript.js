function createPopup(id){
    let popupNode = document.querySelector(id);
    let overlay = popupNode.querySelector(".overlay");
    let closeBtn = popupNode.querySelector(".close-btn");

    let steps = Array.from(popupNode.querySelectorAll('.step'));
    let nextBtn = popupNode.querySelector('.next-btn');
    let currenIndex = 0;


    function showStep(i){
        steps.forEach((el,idx) =>{
            if (idx === i){
                el.hidden = false;
                el.classList.add('is-active');
            }else{
                el.hidden = true;
                el.classList.remove('is-active');
            }
        });
        if(nextBtn){
            nextBtn.textContent = (i === steps.length - 1) ? 'Book':'next';
        }
    }
    if(nextBtn){
        nextBtn.addEventListener('click',()=>{
            if(currenIndex<steps.length-1){
                currenIndex +=1;
                showStep(currenIndex);
            }else{
                closePopup();
            } 
        });
    }

    const _openPopup = openPopup;
    openPopup = function(){
        _openPopup();
        currenIndex = 0;
        if(steps.length)showStep(0);
    };



    function openPopup(){
        popupNode.classList.add("active");
    }
    function closePopup(){
        popupNode.classList.remove("active");
    }
    overlay.addEventListener("click",closePopup);
    closeBtn.addEventListener("click",closePopup);
    return openPopup;
}

let popup = createPopup(".popup");
document.querySelector(".open-popup").addEventListener("click",popup);


// สร้างปุ่ม ทีหลัง
document.addEventListener("click", (e) => {
  const trigger = e.target.closest(".open-popup");
  if (!trigger){
    return;
  }
  popup();
});



function test() {
    alert("jim")
}


