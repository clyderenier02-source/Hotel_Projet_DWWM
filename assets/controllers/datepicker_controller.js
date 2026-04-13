import { Controller } from "@hotwired/stimulus";
import flatpickr from "flatpickr";
import { French } from "flatpickr/dist/l10n/fr.js"
flatpickr.localize(French);

export default class extends Controller {

    static targets = ["arrived", "return", "arrivedContainer", "returnContainer"]

    connect() {
        this.initArrivedPicker()
        this.initReturnPicker(new Date().fp_incr(1))

        // Affiche le calendrier arrivée au clic sur l'input
        this.arrivedTarget.nextSibling.addEventListener("click", () => {
            this.arrivedContainerTarget.classList.add("reservationForm__calendar--visible")
        })
    }

    initArrivedPicker() {
        if (!this.hasArrivedTarget) return

        this.arrivedPicker = flatpickr(this.arrivedTarget, {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "d-m-Y",
            minDate: "today",
            disableMobile: true,
            locale: { firstDayOfWeek: 1 },
            appendTo: this.arrivedContainerTarget,
            inline: true,

            onChange: (dates) => {
                if (!dates[0]) return

                const minReturn = new Date(dates[0])
                minReturn.setDate(minReturn.getDate() + 1)

                if (this.returnPicker?.selectedDates[0]) {
                    const currentReturn = this.returnPicker.selectedDates[0]
                    if (currentReturn <= dates[0]) {
                        this.returnTarget.value = ""
                    }
                }

                this.initReturnPicker(minReturn)

                // Affiche le calendrier retour
                this.returnContainerTarget.classList.add("reservationForm__calendar--visible")
            }
        })
    }

    initReturnPicker(minDate) {
        if (!this.hasReturnTarget) return

        const previousDate = this.returnPicker?.selectedDates[0] || null

        if (this.returnPicker) {
            this.returnPicker.destroy()
        }

        this.returnPicker = flatpickr(this.returnTarget, {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "d-m-Y",
            disableMobile: true,
            minDate: minDate,
            appendTo: this.returnContainerTarget,
            inline: true,
            defaultDate: previousDate && previousDate > minDate ? previousDate : null,

            onReady: (selectedDates, dateStr, instance) => {
                this.highlightArrivalDate(instance)
            },

            onMonthChange: (selectedDates, dateStr, instance) => {
                this.highlightArrivalDate(instance)
            }
        })
    }

    highlightArrivalDate(instance) {
        if (!this.arrivedPicker?.selectedDates[0]) return

        const arrived = new Date(this.arrivedPicker.selectedDates[0])
        arrived.setHours(0, 0, 0, 0)

        instance.calendarContainer.querySelectorAll(".flatpickr-day").forEach(day => {
            day.classList.remove("arrival-date")

            if (day.dateObj) {
                const d = new Date(day.dateObj)
                d.setHours(0, 0, 0, 0)

                if (d.getTime() === arrived.getTime()) {
                    day.classList.add("arrival-date")
                }
            }
        })
    }

    disconnect() {
        this.arrivedPicker?.destroy()
        this.returnPicker?.destroy()
    }
}