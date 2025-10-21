function closeModal(modal){
    modal.removeClass('active');
}
function openModal(modal){
    modal.addClass('active');
}


$('*[open-modal="true"]').click(function(){
    var modal = $(this).attr('modal-id');

    alert(modal);
});



// function test(){
//     alert('jim');
// }