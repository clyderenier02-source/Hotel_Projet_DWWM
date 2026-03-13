import { Controller } from "@hotwired/stimulus";
import flatpickr from "flatpickr";
import { French } from "flatpickr/dist/l10n/fr.js"
flatpickr.localize(French);

export default class extends Controller {

    // Déclare les deux cibles HTML : l'input d'arrivé et de retour
    static targets = ["arrived", "return"]

    connect() {
        this.initReturnPicker(new Date().fp_incr(1))

        if (this.hasArrivedTarget) {
            this.arrivedPicker = flatpickr(this.arrivedTarget, {
                dateFormat: "Y-m-d",
                minDate: "today", // Interdit les dates déja passé
                disableMobile: true, // Force l'affichage flatpickr sur mobile
                locale: { firstDayOfWeek: 1 }, // La semaines commence lundi
                position: "auto center",

                onChange: (dates) => {
                    if (!dates[0]) return

                    // Calcule la date minimum de retour = arrivée + 1 jour
                    const minReturn = new Date(dates[0])
                    minReturn.setDate(minReturn.getDate() + 1)

                    // Vider la date de retour si elle est inférieure ou égale à l'arrivée
                    if (this.returnPicker?.selectedDates[0]) {
                        const currentReturn = this.returnPicker.selectedDates[0]
                        if (currentReturn <= dates[0]) {
                            this.returnTarget.value = ""
                        }
                    }

                    // Recrée le picker de retour avec la nouvelle contrainte
                    this.initReturnPicker(minReturn)
                }
            })
        }
    }

    initReturnPicker(minDate) {
        if (!this.hasReturnTarget) return

        // Sauvegarde la date de retour actuellement sélectionnée avant de détruire le picker
        const previousDate = this.returnPicker?.selectedDates[0] || null

        // Détruit l'ancien picker pour en créer un propre avec le bon minDate
        if (this.returnPicker) {
            this.returnPicker.destroy()
        }

        this.returnPicker = flatpickr(this.returnTarget, {
            dateFormat: "Y-m-d",
            disableMobile: true,
            minDate: minDate,
            position: "auto center",

            // Restaurer la date précédente seulement si elle est encore valide
            defaultDate: previousDate && previousDate > minDate ? previousDate : null,

            // Au moment où le calendrier s'ouvre, met en surbrillance la date d'arrivée
            onOpen: (selectedDates, dateStr, instance) => {
                this.highlightArrivalDate(instance)
            },

            // Quand l'utilisateur change de mois, réapplique la surbrillance
            onMonthChange: (selectedDates, dateStr, instance) => {
                this.highlightArrivalDate(instance)
            }
        })
    }

    highlightArrivalDate(instance) {
        // Si aucune date d'arrivée n'est sélectionnée, rien à faire
        if (!this.arrivedPicker?.selectedDates[0]) return

        const arrived = new Date(this.arrivedPicker.selectedDates[0])
        arrived.setHours(0, 0, 0, 0)

        // Parcourt tous les jours affichés dans le calendrier de retour
        instance.calendarContainer.querySelectorAll(".flatpickr-day").forEach(day => {

            // Retire la classe au cas où elle était déjà là
            day.classList.remove("arrival-date")

            if (day.dateObj) {
                const d = new Date(day.dateObj)
                d.setHours(0, 0, 0, 0)

                // Si le jour correspond à la date d'arrivée, ajoute la classe CSS
                if (d.getTime() === arrived.getTime()) {
                    day.classList.add("arrival-date")
                }
            }
        })
    }

    disconnect() {
        // Nettoie les pickers quand le controller Stimulus est détruit
        this.arrivedPicker?.destroy()
        this.returnPicker?.destroy()
    }
}