window.addEventListener("DOMContentLoaded", () => {

    /************************
     * On-the-go validation *
     ************************/
        let errors= []; // if at any time this boolean is flipped to true, the form wont be submitable
   
        /* Drop-down input Validation (Genre & MPAA)*/
        const Genre = document.querySelector("#Genre");

        Genre.addEventListener('blur',(ev)=>{
           if(Genre.value==0){
            document.querySelector("#Genre+span").classList.remove("hidden");
            errors[0]=true;
        }else{
            document.querySelector("#Genre+span").classList.add("hidden");
            errors[0]=false;
        } 
        })
        
        const MPAA = document.querySelector("#MPAA");

        MPAA.addEventListener('blur',(ev)=>{
           if(MPAA.value==0){
            document.querySelector("#MPAA+span").classList.remove("hidden");
            errors[1]=true;
        }else{
            document.querySelector("#MPAA+span").classList.add("hidden");
            errors[1]=false;

        } 
        })
        

    /* Year input validation (Year, theatre release, dvd/streaming release) */

        const Year = document.querySelector("#Year");

        Year.addEventListener('blur', (ev)=>{
            if(Number.isInteger(Number(Year.value))===false){
                document.querySelector("#Year+span").classList.remove("hidden");  
                errors[2]=true;          
            }
            else{
                current = new Date();
                if(Number(Year.value) <1900 || Number(Year.value)>current.getFullYear()){
                    document.querySelector("#Year+span").classList.remove("hidden");
                    errors[2]=true
                }else{
                    document.querySelector("#Year+span").classList.add("hidden");
                    errors[2]=false;
                }
            }
        });

        const TheatreRelease = document.querySelector("#TheatreRelease");

        TheatreRelease.addEventListener('blur', (ev)=>{
       
            if(Number(TheatreRelease.value) !=0){

                // if the user enters a value in the field, make sure the first error is hidden
                document.querySelector("#TheatreRelease+span").classList.add("hidden");

                //then check if the input is a number
                if(Number.isInteger(Number(TheatreRelease.value))===false){

                    //if it is not a number, show the second error
                    document.querySelector("#TheatreRelease+span+span").classList.remove("hidden");  
                    errors[3]=true;          
                }
                else{
                    current = new Date();
                    if(Number(TheatreRelease.value) <1900 || Number(TheatreRelease.value)>current.getFullYear()){
                        //if the number is less than 1900 or more than trhe current year, also show the second error
                        document.querySelector("#TheatreRelease+span+span").classList.remove("hidden");                          
                        errors[3]=true;
                    }else{
                        //if the input is valid, hide both errors
                        document.querySelector("#TheatreRelease+span").classList.add("hidden");
                        document.querySelector("#TheatreRelease+span+span").classList.add("hidden");
                        errors[3]=false;

                    }
                }
            }else{
                //if the user does not enter a value, or enters 0, hide one error and show the other
                document.querySelector("#TheatreRelease+span+span").classList.add("hidden");
                document.querySelector("#TheatreRelease+span").classList.remove("hidden");
                errors[3]=true;

            }
        })
        
        const DVDRelease = document.querySelector("#DVDrelease");

        DVDRelease.addEventListener('blur', (ev)=>{
       
            if(Number(DVDRelease.value) !=0){
                // if the user enters a value in the field, make sure the first error is hidden
                document.querySelector("#DVDrelease+span").classList.add("hidden");
                //then check if the input is a number
                if(Number.isInteger(Number(DVDRelease.value))==false){
                    //if it is not a number, show the second error
                    document.querySelector("#DVDrelease+span+span").classList.remove("hidden");  
                    errors[4]=true;          
                }
                else{
                    current = new Date();
                    if(Number(DVDRelease.value) <1900 || Number(DVDRelease.value)>current.getFullYear()){
                        //if the number is less than 1900 or more than trhe current year, also show the second error
                        document.querySelector("#DVDrelease+span+span").classList.remove("hidden");                          
                        errors[4]=true;
                    }else{
                        //if the input is valid, hide both errors
                        document.querySelector("#DVDrelease+span").classList.add("hidden");
                        document.querySelector("#DVDrelease+span+span").classList.add("hidden");
                        errors[3]=false;
                    }
                }
            }else{
                //if the user does not enter a value, or enters 0, hide one error and show the other
                document.querySelector("#DVDrelease+span+span").classList.add("hidden");
                document.querySelector("#DVDrelease+span").classList.remove("hidden");
                errors[4]=true;

            }
        })
        
    /* Run-Time validation (either field must be filled) */
    
        const Minutes=document.querySelector("#RunTimeMinutes");
        const Hours=document.querySelector("#RunTimeHours");

        Minutes.addEventListener('blur',(ev)=>{
            // if both fields are empty, show the third error
            if(Minutes.value=="" & Hours.value==""){
                document.getElementById('minerr3').classList.remove("hidden");
                errors[5]=true;
            }else{
                document.getElementById('minerr3').classList.add("hidden");
                errors[5]=false;
            }

            //if the input is not a number, show the second error
            if(Number.isInteger(Number(Minutes.value))==false){
                document.getElementById('minerr2').classList.remove("hidden");
                errors[5]=true;
            }else{
                document.getElementById('minerr2').classList.add("hidden");
                errors[5]=false;
            }
        })

        Hours.addEventListener('blur',(ev)=>{
            // if both fields are empty, show the third error
            if(Hours.value=="" & Minutes.value==""){
                document.getElementById('minerr3').classList.remove("hidden");
                errors[6]=true;
            }else{
                document.getElementById('minerr3').classList.add("hidden");
                errors[6]=false;

            }
            //if the input is not a number, show the second error
            if(Number.isInteger(Number(Hours.value))==false){
                document.getElementById('minerr2').classList.remove("hidden");
                errors[6]=true;
            }else{
                document.getElementById('minerr2').classList.add("hidden");
                errors[6]=false;
            }
        });

        /* Plot Summary validation and character count functionality*/
        const TextArea=document.getElementById("PlotSummary");
        const TextCount=document.getElementById("PlotSummary-counter");

        /* set the character count on page load in case there is anything prefilled */
        TextCount.textContent =`${2500-TextArea.value.length} / 2500`;

        /* Simple Event Listener to update character count */
        TextArea.addEventListener("input", ev=>{
            const Count=2500-TextArea.value.length;
            TextCount.textContent =`${Count} / 2500`;
            if(TextArea.value.length==0){
                errors[7]=true;
            }else{
                errors[7]=false;
            }         
        })

        /*************************************************
         Actors validation. There isn't an hard limit on 
         how many spaces should be allowed before a comma, 
         because the amount of words in people's full names
                   is different across cultures
        **************************************************/
        const Actors =document.getElementById("Actors");

        Actors.addEventListener("blur", ev=>{

            /******************************************************
             if the user insputs a string with more than 3 spaces
                that has no commas, remind them to use commas 
            *******************************************************/

            numspaces= Actors.value.length - Actors.value.replace(/\s/gi,"").length;
            numcommas= Actors.value.length - Actors.value.replace(/,/gi,"").length;
            if(numspaces>3 && numcommas==0){
                document.getElementById("Actors").nextElementSibling.nextElementSibling.setAttribute("style","color:purple;");

                document.getElementById("Actors").nextElementSibling.nextElementSibling.classList.remove("hidden");
            }else{
                document.getElementById("Actors").nextElementSibling.nextElementSibling.classList.add("hidden");
            }
            if(Actors.value.length==0){
                document.getElementById("Actors").nextElementSibling.classList.remove("hidden");
                errors[8]=true;
            }
            else{
                document.getElementById("Actors").nextElementSibling.classList.add("hidden");
                errors[8]=false;
            }
            
        });
        /* Tile Validation */
        const Title=document.getElementById("Title");
        Title.addEventListener("blur",ev=>{
            if(Title.value.length==0){
                document.getElementById("Title").nextElementSibling.classList.remove("hidden");
                errors[9]=true;
            }else{
                document.getElementById("Title").nextElementSibling.classList.add("hidden");
                errors[9]=false;
            }
        })

        const Form=document.getElementById("Add-video-form");
        Form.addEventListener("submit",ev=>{
        if(errors.length==0){            
            if(document.URL.includes("addvid.php")==true){
            ev.preventDefault();
            }
        }else{
            
            for(const error of errors){
                if(error==true){ev.preventDefault();}
            }            
            
        }
     })
        
});