window.addEventListener("DOMContentLoaded", () => {
    /* These are used for validation */
    UserValid=false;
    EmailValid=false;
    ReEmailValid=false;
    PassValid=false;
    RePassValid=false;

    
    /* add blur event listener to uername field to validate */
    document.getElementById("username").addEventListener("blur", ev=>{
         Entry = document.querySelector("#username");
         /* if nothing was entered, show an error, otherwise hide it */
        if(Entry.value.length==0){
           
            Entry.nextElementSibling.classList.add("hidden");
            Entry.nextElementSibling.nextElementSibling.classList.remove("hidden");
            UserValid=false;
        }else{
            Entry.nextElementSibling.nextElementSibling.classList.add("hidden");
            /* Use XMLHttpRequet() to pass the entry to RegCheck.php to ckeck if it already exists */
            const xhr = new XMLHttpRequest();

            xhr.open('GET',`https://loki.trentu.ca/~marctouma/3420/assignments/assn3/RegCheck.php?username=${Entry.value}`)

            xhr.addEventListener('load', ev =>{

            if(xhr.status==200){
                /* If it already exists, or an error is returned, show an error. Otherwise hide the error */
                if(xhr.response=="true" || xhr.response=="error"){
                    UserValid=false;
                    Entry.nextElementSibling.classList.remove("hidden");
                }else{
                    UserValid=true;
                    Entry.nextElementSibling.classList.add("hidden");
                }
            }      
            });

            xhr.send();
        }
    });

    /* This event listener does the same thing but to the Email field */
    document.getElementById("Email").addEventListener("blur", ev=>{
        Entry = document.querySelector("#Email");
        if(Entry.value==""){
            Entry.nextElementSibling.nextElementSibling.classList.remove("hidden");
            Entry.nextElementSibling.classList.add("hidden");
            EmailValid=false;
        }else{
            Entry.nextElementSibling.nextElementSibling.classList.add("hidden");
            const xhr = new XMLHttpRequest();
            xhr.open('GET',`https://loki.trentu.ca/~marctouma/3420/assignments/assn3/RegCheck.php?Email=${Entry.value}`);
            xhr.addEventListener('load', ev =>{
            if(xhr.status==200){
                if(xhr.response=="true" || xhr.response=="error"){
                    EmailValid=false;
                    
                    Entry.nextElementSibling.classList.remove("hidden");
                }else{
                    EmailValid=true;
                    Entry.nextElementSibling.classList.add("hidden");                    
                }
            }      
            });
            xhr.send();
        }
        ReEmail=document.getElementById("Re-Email");
        //check the Re-enter password field for an entry thats not equal to the password fields entry
        if(ReEmail.value !=""){
            ReEmail.focus();
            ReEmail.blur();
        }
    });

    /**********************************************************************
    This function was sourced from stack overflow from the following link: 
    https://stackoverflow.com/questions/948172/password-strength-meter 
    ***********************************************************************/
    function scorePassword(pass) {
        var score = 0;
        if (!pass)
            return score;
    
        // award every unique letter until 5 repetitions
        var letters = new Object();
        for (var i=0; i<pass.length; i++) {
            letters[pass[i]] = (letters[pass[i]] || 0) + 1;
            score += 5.0 / letters[pass[i]];
        }
    
        // bonus points for mixing it up
        var variations = {
            digits: /\d/.test(pass),
            lower: /[a-z]/.test(pass),
            upper: /[A-Z]/.test(pass),
            nonWords: /\W/.test(pass),
        }
    
        var variationCount = 0;
        for (var check in variations) {
            variationCount += (variations[check] == true) ? 1 : 0;
        }
        score += (variationCount - 1) * 10;
    
        return parseInt(score);
    }
    /* This event listener validates password and gives funtionality to the password strength visual*/
    document.getElementById("Password").addEventListener("blur", ev=>{
        Entry=document.getElementById("Password");
        //if the user did not enter any password, hide all errors except for the appropriate one
        if(Entry.value==""){
            PassValid=false;
            document.querySelector("meter#meter").classList.add("hidden");
            document.getElementById("PassMeter").style.display="none";            
            document.getElementById("PassMess").classList.add("hidden");
            document.getElementById("PassMess1").classList.add("hidden");
            document.getElementById("PassMess2").classList.add("hidden");
            document.getElementById("PassMess4").classList.remove("hidden")
        }
        /*if the user did enter something, hide the error that would 
        have been shown otherwise and reveal the password strength indicator*/
        else{            
            document.querySelector("meter#meter").classList.remove("hidden");
            document.getElementById("PassMess4").classList.add("hidden")
            document.getElementById("PassMeter").style.display="flex";  
            /* Call the score password function to measure the password strength and assign its value to the meter */
            Score=scorePassword(Entry.value);
            document.getElementsByTagName("meter")[0].setAttribute("value", Score);
        
            /*These if statements determine which message should be displayed along side the meter*/
            if(Score<50){
                PassValid=false;
                document.getElementById("PassMess").classList.remove("hidden");
            }else{
                document.getElementById("PassMess").classList.add("hidden");
            }

            if(Score>50 && Score<80){
                document.getElementById("PassMess1").style.color="green";
                document.getElementById("PassMess1").classList.remove("hidden");
                PassValid=true;
            }else{
                document.getElementById("PassMess1").classList.add("hidden");
            }

            if(Score>80){
                document.getElementById("PassMess2").style.color="green"
                document.getElementById("PassMess2").classList.remove("hidden");
                PassValid=true;
            }else{
                document.getElementById("PassMess2").classList.add("hidden");
            }
        }

        RePass=document.getElementById("Re-Password");
        //check the Re-enter password field for an entry thats not equal to the password fields entry
        if(RePass.value !=""){
            RePass.focus();
            RePass.blur();
        }


    });

    /* This event listener validates the re-entered password*/
    document.getElementById("Re-Password").addEventListener("blur", ev=>{
       const Entry=document.getElementById("Re-Password");
        if(Entry.value==""){
            Entry.nextElementSibling.classList.remove("hidden");
            RePassValid=false;
        }
        else{
            Entry.nextElementSibling.classList.add("hidden");
            if(Entry.value==document.getElementById("Password").value){
                RePassValid=true;
                Entry.nextElementSibling.nextElementSibling.classList.add("hidden");
            }
            else{
                RePassValid=false;
                Entry.nextElementSibling.nextElementSibling.classList.remove("hidden");
            }
        }
    });


    document.getElementById("Re-Email").addEventListener("blur", ev=>{
        const Entry=document.getElementById("Re-Email");
         if(Entry.value==""){
             Entry.nextElementSibling.classList.remove("hidden");
             ReEmailValid=false;
         }
         else{
             Entry.nextElementSibling.classList.add("hidden");
             if(Entry.value==document.getElementById("Email").value){
                 ReEmailValid=true;
                 Entry.nextElementSibling.nextElementSibling.classList.add("hidden");
             }
             else{
                 ReEmailValid=false;
                 Entry.nextElementSibling.nextElementSibling.classList.remove("hidden");
             }
         }
     });

     const Form=document.getElementById("Register-Account-form");
     Form.addEventListener("submit",ev=>{
        if(UserValid==false || EmailValid==false ||PassValid==false || RePassValid==false ||ReEmailValid==false){
            ev.preventDefault();
        }
     })
});