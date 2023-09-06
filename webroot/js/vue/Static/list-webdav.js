var app = new Vue({
    el: '#app',
    data() {
        return {
            result: [],
            loading: false,
            spinning: false,
            progress: 0,
            max: 100,
            current_action: "Importazione da NextCloud ...",
            stop: false,
        }
    },
    created() {
            this.loading = false;
    },
    methods: {
        async runList(origin = '') {
            if (this.stop)   return;
            this.spinning = true; 
            this.result = "Scaricamento in corso $origin";
            this.current_action = `Inizio scaricamento  ${origin}`;
            let url = '/admin/static/list-webdav.json';
            
            try {
                this.result = await axios.post(url, {origin: origin});
                let res = this.result.data;
                this.max += res.length;
                //loop on the array of results and call the right function depedning on action
                for (let index = 0; index < res.length; index++) {
                    if (this.stop)   return;
                    const e = res[index];
                    this.progress++;
                    if (e.action == "list"){
                        //scarico ricorsivamente    
                        console.log("scarico ricorsivamente", e.descr);
                        this.current_action = `Scarico ricorsivamente ${e.descr}`;
                        await this.runList(e.file_url);
                    } else if (e.action == "get") {
                        console.log("get file", e.descr);
                        this.current_action = `Scarico file ${e.descr}`;
                        await this.doGet(e);
                    } else {
                        this.current_action = e.descr;
                    }                  
                }
                console.log("result", res);
                this.current_action = `Finito`;
                console.log("finito");
                this.progress = this.max;
                this.spinning = false;    
            } catch (error) {
                console.log("ERROR", error);
                this.current_action = `Errore: ${error}`;
                this.spinning = false;             
            }            
        },

        async doGet(e) {
            //scarico la singola risorsa
            console.log("doGet", e);
            this.current_action = `Scaricamento in corso ${e.filename}`;
            await axios.post(`/admin/static/get-webdav.json`,{
                filename: e.filename,
                file_url: e.file_url,
                last_modified_remote: e.last_modified_remote,
            });
            console.log("doGet", e, "fatto");
            this.current_action = `OK - Scaricamento completato ${e.filename}`;
        },

        removeTrailingSlash(str) {
            return str.replace(/\/+$/, '');
        },

        doStop() {
            this.stop = true;
            this.progress = 0;
            this.spinning = false;
            this.current_action = "importazione interrotta."
        }
    }
});