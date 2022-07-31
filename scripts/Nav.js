
window.addEventListener("DOMContentLoaded", () => {
//grab the dropdown buttons
const chevron1 = document.getElementsByClassName('dropdown 1');
const chevron2 = document.getElementsByClassName('dropdown 2');

/*************************************************
 add click and blur event listeners to 
 appropriately hide or display the dropdown menu
 ************************************************/
//these bools track whether or not the dropdown has been opened
C1=false;
C2=false;

chevron1[0].addEventListener('click', (ev)=>{
    if(C1==false){
        document.getElementsByClassName('dropdown-container 1')[0].style.display="initial";
        C1=true;
    }else{
        document.getElementsByClassName('dropdown-container 1')[0].style.display="none";
        C1=false;
    }

});

chevron2[0].addEventListener('click', (ev)=>{
    if(C2==false){
        document.getElementsByClassName('dropdown-container 2')[0].style.display="initial";
        C2=true;
    }else{
        document.getElementsByClassName('dropdown-container 2')[0].style.display="none";
        C2=false;
    } 
});

});
