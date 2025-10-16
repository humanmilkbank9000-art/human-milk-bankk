document.addEventListener('DOMContentLoaded', function(){
    try{
        const sidebar = document.querySelector('.sidebar');
        if(!sidebar) return;
        const links = Array.from(sidebar.querySelectorAll('a'));
        const key = 'sidebarClickedHref';

        function normalizeHrefVal(val){
            if(!val) return '';
            try{ return val.replace(window.location.origin, ''); }catch(e){ return val; }
        }

        // restore saved clicked link
        const saved = sessionStorage.getItem(key);
        if(saved){
            links.forEach(a => {
                const ah = a.getAttribute('href') || a.href || '';
                if(ah && (ah === saved || ah === (window.location.origin + saved) || normalizeHrefVal(ah) === normalizeHrefVal(saved))){
                    a.classList.add('clicked');
                }
            });
        }

        links.forEach(a => {
            a.addEventListener('click', function(e){
                const href = this.getAttribute('href') || this.href || '';
                try{ sessionStorage.setItem(key, href); }catch(err){}
                links.forEach(l => l.classList.remove('clicked'));
                this.classList.add('clicked');
            });
        });
    }catch(err){ console && console.error && console.error('sidebar click persistence err', err); }
});
