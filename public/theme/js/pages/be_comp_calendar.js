!(function () {
    class e {
        static addEvent() {
            let e = document.querySelector(".js-add-event"),
                t = "";
            document.querySelector(".js-form-add-event").addEventListener("submit", (a) => {
                if ((a.preventDefault(), (t = e.value), t)) {
                    let a = document.getElementById("js-events"),
                        n = document.createElement("li"),
                        l = document.createElement("div");
                    l.classList.add("js-event"),
                        l.classList.add("p-2"),
                        l.classList.add("fs-sm"),
                        l.classList.add("fw-medium"),
                        l.classList.add("rounded"),
                        l.classList.add("bg-info-light"),
                        l.classList.add("text-info"),
                        (l.textContent = t),
                        n.appendChild(l),
                        a.insertBefore(n, a.firstChild),
                        (e.value = "");
                }
            });
        }
        static initEvents() {
            new FullCalendar.Draggable(document.getElementById("js-events"), {
                itemSelector: ".js-event",
                eventData: function (e) {
                    return { title: e.textContent, backgroundColor: getComputedStyle(e).color, borderColor: getComputedStyle(e).color };
                },
            });
        }
        static initCalendar() {
            /* let e = new Date(),
                t = e.getDate(),
                a = e.getMonth(),
                n = e.getFullYear();
            let newData = [
                { title: "TR-CLASS1", start: new Date(n, a, 1), allDay: !0,color:"#82b54b" , url: classurl },
                { title: "TR-CLASS2", start: new Date(n, a, 3), allDay: !0,color:"#e04f1a" , url: classurl },
                { title: "IN-CLASS3", start: new Date(n, a, 5), allDay: !0, url: classurl },
                { title: "IN-CLASS4", start: new Date(n, a, 7), allDay: !0,color:"#82b54b" , url: classurl },
                { title: "TR-CLASS5", start: new Date(n, a, 9), allDay: !0,color:"#e04f1a" , url: classurl },
                { title: "IN-CLASS6", start: new Date(n, a, 11), allDay: !0, url: classurl },
                { title: "IN-CLASS7", start: new Date(n, a, 13), allDay: !0, url: classurl },
                { title: "TR-CLASS8", start: new Date(n, a, 15), allDay: !0,color:"#e04f1a" , url: classurl },
                { title: "IN-CLASS9", start: new Date(n, a, 17), allDay: !0, url: classurl },
                { title: "TR-CLASS10", start: new Date(n, a, 19), allDay: !0,color:"#82b54b" , url: classurl },
            ]; */
            let data = JSON.parse(document.getElementById("js-calendar").getAttribute("data"));
            let newData = [];
            if(data){
                data.forEach(element => {
                    console.log(element);
                    let obj = { title: element.title, start: new Date(element.date), allDay: !0,color:element.color , url: base+'/class-detail/'+element.id };
                    newData.push(obj);
                });
            }
            new FullCalendar.Calendar(document.getElementById("js-calendar"), {
                themeSystem: "standard",
                firstDay: 1,
                editable: !0,
                droppable: !0,
                headerToolbar: { left: "title", right: "prev,next today dayGridMonth,timeGridWeek,timeGridDay,listWeek" },
                drop: function (e) {
                    e.draggedEl.parentNode.remove();
                },
                events: newData,
            }).render();
        }
        static init() {
            let element = document.getElementById("js-calendar");
            if (typeof(element) != 'undefined' && element != null)
            {
                //this.addEvent(), this.initEvents(), this.initCalendar();
                this.initCalendar();
            }
        }
    }
    One.onLoad(() => e.init());
})();
