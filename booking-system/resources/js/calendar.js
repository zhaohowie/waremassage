import { Calendar } from '@fullcalendar/core';
import resourceTimeGridPlugin from '@fullcalendar/resource-timegrid';
import interactionPlugin from '@fullcalendar/interaction';
import timeGridPlugin from '@fullcalendar/timegrid';
import dayGridPlugin from '@fullcalendar/daygrid';

document.addEventListener('DOMContentLoaded', function () {
    const el = document.getElementById('calendar');

    if (!el) return;

    const calendar = new Calendar(el, {
        schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives',

        plugins: [
            resourceTimeGridPlugin,
            interactionPlugin,
            timeGridPlugin,
            dayGridPlugin
        ],

        initialView: 'resourceTimeGridDay',
        initialDate: window.calendarData.date,

        resources: window.calendarData.resources,
        events: window.calendarData.events,

        titleFormat: {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            weekday: 'long'
        },

        editable: true,
        selectable: true,
        allDaySlot: false,
        nowIndicator: true,

        slotDuration: '00:15:00',
        slotLabelInterval: '01:00',
        slotMinTime: '09:00:00',
        slotMaxTime: '22:00:00',
        snapDuration: '00:05:00',

        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'resourceTimeGridDay,timeGridWeek,dayGridMonth'
        },

        datesSet: function(info) {
            const newDate = info.startStr.substring(0, 10);
            loadCalendarData(newDate);
        },

        eventDrop: function(info) {
            moveAppointment(info);
        },

        eventResize: function(info) {
            moveAppointment(info);
        },

        dateClick: function(info) {
            openSlotContextMenu(info);
        },

        eventClick: function(info) {
            info.jsEvent.preventDefault();
            info.jsEvent.stopPropagation();
            if (info.event.extendedProps.type === 'blocked_time') {
                openBlockedTimeModal(info.event);
                return;
            }

            openAppointmentContextMenu(info);
        },
    });

    calendar.render();

    let selectedAppointmentEvent = null;

    function openAppointmentContextMenu(info) {
        selectedAppointmentEvent = info.event;

        const noShowButton = document.getElementById('appointment-no-show');

        if (noShowButton) {
            if (selectedAppointmentEvent.extendedProps.status === 'no_show') {
                noShowButton.innerText = 'Undo No Show';
            } else {
                noShowButton.innerText = 'Set No Show';
            }
        }

        let menu = document.getElementById('appointment-context-menu');

        if (!menu) {
            menu = document.createElement('div');
            menu.id = 'appointment-context-menu';
            menu.style.position = 'fixed';
            menu.style.background = '#ffffff';
            menu.style.border = '1px solid #ddd';
            menu.style.borderRadius = '8px';
            menu.style.boxShadow = '0 8px 20px rgba(0,0,0,0.15)';
            menu.style.zIndex = '9999';
            menu.style.minWidth = '190px';
            menu.style.overflow = 'hidden';

            menu.innerHTML = `
                <button type="button" id="appointment-edit"
                        style="display:block; width:100%; padding:10px 12px; border:none; background:white; text-align:left; cursor:pointer;">
                    Edit Appointment
                </button>

                <button type="button" id="appointment-view-activities"
                        style="display:block; width:100%; padding:10px 12px; border:none; background:white; text-align:left; cursor:pointer;">
                    View Appointment Activities
                </button>    

                 <button type="button" id="appointment-add-soap"
                        style="display:block; width:100%; padding:10px 12px; border:none; background:white; text-align:left; cursor:pointer;">
                    Add SOAP Note
                </button>

                <button type="button" id="appointment-no-show"
                        style="display:block; width:100%; padding:10px 12px; border:none; background:white; text-align:left; cursor:pointer;">
                    Set No Show
                </button>
                `;

            document.body.appendChild(menu);

            document.getElementById('appointment-add-soap').addEventListener('click', function () {
                if (!selectedAppointmentEvent) return;

                const appointmentDate = selectedAppointmentEvent.startStr.substring(0, 10);

                const returnUrl = `/calendar?date=${appointmentDate}`;

                window.location.href =
                    `/appointments/${selectedAppointmentEvent.id}/soap-notes?return_url=` +
                    encodeURIComponent(returnUrl);
            });

            document.getElementById('appointment-no-show').addEventListener('click', function () {
                if (!selectedAppointmentEvent) return;

                if (selectedAppointmentEvent.extendedProps.status === 'no_show') {
                    undoAppointmentNoShow(selectedAppointmentEvent);
                } else {
                    setAppointmentNoShow(selectedAppointmentEvent);
                }
            });

            document.getElementById('appointment-view-activities').addEventListener('click', function () {
                if (!selectedAppointmentEvent) return;

                const appointmentDate = selectedAppointmentEvent.startStr.substring(0, 10);
                const returnUrl = `/calendar?date=${appointmentDate}`;

                window.location.href =
                    `/appointments/${selectedAppointmentEvent.id}/activities?return_url=` +
                    encodeURIComponent(returnUrl);
            });  
                        
            document.getElementById('appointment-edit').addEventListener('click', function () {
                if (!selectedAppointmentEvent) return;

                const returnUrl = `/calendar?date=${selectedAppointmentEvent.startStr.substring(0, 10)}`;

                window.location.href =
                    `/appointments/${selectedAppointmentEvent.id}/edit?return_url=` +
                    encodeURIComponent(returnUrl);
            });            
        }

        menu.style.left = info.jsEvent.clientX + 'px';
        menu.style.top = info.jsEvent.clientY + 'px';
        menu.style.display = 'block';
    }

    function setAppointmentNoShow(event) {
        fetch(`/appointments/${event.id}/no-show`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': window.calendarData.csrf
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                event.setProp('backgroundColor', '#dc2626');
                event.setProp('borderColor', '#b91c1c');
                event.setExtendedProp('status', 'no_show');

                const menu = document.getElementById('appointment-context-menu');
                if (menu) {
                    menu.style.display = 'none';
                }
            } else {
                alert('Could not set no show.');
            }
        })
        .catch(() => {
            alert('Could not set no show.');
        });
    }

    function undoAppointmentNoShow(event) {
        fetch(`/appointments/${event.id}/undo-no-show`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': window.calendarData.csrf
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                event.setExtendedProp('status', 'confirmed');

                if (event.extendedProps.service_color) {
                    event.setProp('backgroundColor', event.extendedProps.service_color);
                    event.setProp('borderColor', event.extendedProps.service_color);
                }

                const menu = document.getElementById('appointment-context-menu');
                if (menu) menu.style.display = 'none';
            } else {
                alert('Could not undo no show.');
            }
        })
        .catch(() => {
            alert('Could not undo no show.');
        });
    }

    function openBlockedTimeModal(event) {
        let modal = document.getElementById('blocked-time-modal');

        if (!modal) {
            modal = document.createElement('div');
            modal.id = 'blocked-time-modal';
            modal.style.position = 'fixed';
            modal.style.inset = '0';
            modal.style.background = 'rgba(0,0,0,0.45)';
            modal.style.zIndex = '10000';
            modal.style.display = 'flex';
            modal.style.alignItems = 'center';
            modal.style.justifyContent = 'center';

            modal.innerHTML = `
                <div style="background:white; width:420px; border-radius:10px; padding:24px;">
                    <h2 style="font-size:20px; font-weight:bold; margin-bottom:16px;">
                        Blocked Time Details
                    </h2>

                    <input type="hidden" id="blocked-time-id">

                    <div style="margin-bottom:12px;">
                        <label>Date</label>
                        <input type="date" id="blocked-time-date"
                            style="width:100%; padding:8px; border:1px solid #d1d5db; border-radius:6px;">
                    </div>

                    <div style="margin-bottom:12px;">
                        <label>Start Time</label>
                        <input type="time" id="blocked-time-start"
                            style="width:100%; padding:8px; border:1px solid #d1d5db; border-radius:6px;">
                    </div>

                    <div style="margin-bottom:12px;">
                        <label>End Time</label>
                        <input type="time" id="blocked-time-end"
                            style="width:100%; padding:8px; border:1px solid #d1d5db; border-radius:6px;">
                    </div>

                    <div style="display:flex; justify-content:space-between; margin-top:20px;">
                        <button type="button" id="blocked-time-delete"
                                style="background:#dc2626; color:white; padding:8px 14px; border-radius:6px;">
                            Delete
                        </button>

                        <div>
                            <button type="button" id="blocked-time-cancel"
                                    style="background:#e5e7eb; padding:8px 14px; border-radius:6px; margin-right:8px;">
                                Cancel
                            </button>

                            <button type="button" id="blocked-time-save"
                                    style="background:#2563eb; color:white; padding:8px 14px; border-radius:6px;">
                                Save
                            </button>
                        </div>
                    </div>
                </div>
            `;

            document.body.appendChild(modal);

            document.getElementById('blocked-time-cancel').addEventListener('click', closeBlockedTimeModal);
            document.getElementById('blocked-time-save').addEventListener('click', saveBlockedTimeFromModal);
            document.getElementById('blocked-time-delete').addEventListener('click', deleteBlockedTimeFromModal);
        }

        document.getElementById('blocked-time-id').value = event.extendedProps.working_hour_id;
        document.getElementById('blocked-time-date').value = event.startStr.substring(0, 10);
        document.getElementById('blocked-time-start').value = event.startStr.substring(11, 16);
        document.getElementById('blocked-time-end').value = event.endStr.substring(11, 16);

        modal.style.display = 'flex';
    }

    let selectedCalendarSlot = null;

    function openSlotContextMenu(info) {
        selectedCalendarSlot = {
            dateStr: info.dateStr,
            staffId: info.resource ? info.resource.id : null,
        };

        let menu = document.getElementById('calendar-context-menu');

        if (!menu) {
            menu = document.createElement('div');
            menu.id = 'calendar-context-menu';
            menu.style.position = 'fixed';
            menu.style.background = '#ffffff';
            menu.style.border = '1px solid #ddd';
            menu.style.borderRadius = '8px';
            menu.style.boxShadow = '0 8px 20px rgba(0,0,0,0.15)';
            menu.style.zIndex = '9999';
            menu.style.minWidth = '190px';
            menu.style.overflow = 'hidden';

            menu.innerHTML = `
                <button type="button" id="ctx-book-appointment"
                        style="display:block; width:100%; padding:10px 12px; border:none; background:white; text-align:left; cursor:pointer;">
                    Book Appointment
                </button>

                <button type="button" id="ctx-block-time"
                        style="display:block; width:100%; padding:10px 12px; border:none; background:white; text-align:left; cursor:pointer;">
                    Block Time
                </button>
            `;

            document.body.appendChild(menu);

            document.getElementById('ctx-book-appointment').addEventListener('click', function () {
                if (!selectedCalendarSlot) return;

                const date = selectedCalendarSlot.dateStr.substring(0, 10);
                const time = selectedCalendarSlot.dateStr.substring(11, 16);
                const staffId = selectedCalendarSlot.staffId;

                window.location.href = `/booking?date=${date}&time=${time}&staff_id=${staffId}`;
            });

            document.getElementById('ctx-block-time').addEventListener('click', function () {
                if (!selectedCalendarSlot) return;

                const date = selectedCalendarSlot.dateStr.substring(0, 10);
                const time = selectedCalendarSlot.dateStr.substring(11, 16);
                const staffId = selectedCalendarSlot.staffId;

                const currentCalendarDate = calendar.getDate().toISOString().substring(0, 10);

                window.location.href =
                    `/staff/${staffId}/block-time?date=${date}` +
                    `&start_time=${time}` +
                    `&return_url=` +
                    encodeURIComponent(`/calendar?date=${currentCalendarDate}`);
            });
        }

        menu.style.left = info.jsEvent.clientX + 'px';
        menu.style.top = info.jsEvent.clientY + 'px';
        menu.style.display = 'block';
    }

    document.addEventListener('click', function (event) {
        const menu = document.getElementById('calendar-context-menu');

        if (menu && !menu.contains(event.target)) {
            menu.style.display = 'none';
        }

        const appointmentMenu = document.getElementById('appointment-context-menu');

        if (appointmentMenu && !appointmentMenu.contains(event.target)) {
            appointmentMenu.style.display = 'none';
        }
    });

    function loadCalendarData(date) {
        fetch(`/calendar/data?date=${date}`)
            .then(response => response.json())
            .then(data => {
                calendar.batchRendering(function () {
                    calendar.getResources().forEach(resource => resource.remove());
                    calendar.getEvents().forEach(event => event.remove());

                    data.resources.forEach(resource => {
                        calendar.addResource(resource);
                    });

                    data.events.forEach(event => {
                        calendar.addEvent(event);
                    });
                });
            });
    }

    function moveAppointment(info) {
        const eventType = info.event.extendedProps.type;

        if (eventType === 'blocked_time') {
            moveBlockedTime(info);
            return;
        }

        const appointmentId = info.event.id;
        const staffId = info.event.getResources()[0]?.id;

        fetch(`/appointments/${appointmentId}/move`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.calendarData.csrf
            },
            body: JSON.stringify({
                staff_id: staffId,
                appointment_date: info.event.startStr.substring(0, 10),
                appointment_time: info.event.startStr.substring(11, 16)
            })
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                info.revert();
                alert('Move failed.');
            }
        })
        .catch(() => {
            info.revert();
            alert('Move failed.');
        });
    }

    function moveBlockedTime(info) {
        const workingHourId = info.event.extendedProps.working_hour_id;
        const staffId = info.event.getResources()[0]?.id;

        fetch(`/blocked-times/${workingHourId}/move`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.calendarData.csrf
            },
            body: JSON.stringify({
                staff_id: staffId,
                specific_date: info.event.startStr.substring(0, 10),
                start_time: info.event.startStr.substring(11, 16),
                end_time: info.event.endStr.substring(11, 16)
            })
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                info.revert();
                alert('Blocked time move failed.');
            }
        })
        .catch(() => {
            info.revert();
            alert('Blocked time move failed.');
        });
    }

    function deleteBlockedTime(event) {
        const workingHourId = event.extendedProps.working_hour_id;

        fetch(`/blocked-times/${workingHourId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': window.calendarData.csrf
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                event.remove();
            } else {
                alert('Delete failed.');
            }
        })
        .catch(() => {
            alert('Delete failed.');
        });
    }

    function closeBlockedTimeModal() {
        const modal = document.getElementById('blocked-time-modal');

        if (modal) {
            modal.style.display = 'none';
        }
    }

    function saveBlockedTimeFromModal() {
        const id = document.getElementById('blocked-time-id').value;
        const date = document.getElementById('blocked-time-date').value;
        const start = document.getElementById('blocked-time-start').value;
        const end = document.getElementById('blocked-time-end').value;

        const event = calendar.getEventById('blocked-' + id);
        const staffId = event.getResources()[0]?.id;

        fetch(`/blocked-times/${id}/move`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.calendarData.csrf
            },
            body: JSON.stringify({
                staff_id: staffId,
                specific_date: date,
                start_time: start,
                end_time: end
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeBlockedTimeModal();
                loadCalendarData(date);
            } else {
                alert('Update failed.');
            }
        })
        .catch(() => alert('Update failed.'));
    }

    function deleteBlockedTimeFromModal() {
        const id = document.getElementById('blocked-time-id').value;

        if (!confirm('Delete this blocked time?')) {
            return;
        }

        fetch(`/blocked-times/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': window.calendarData.csrf
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const event = calendar.getEventById('blocked-' + id);

                if (event) {
                    event.remove();
                }

                closeBlockedTimeModal();
            } else {
                alert('Delete failed.');
            }
        })
        .catch(() => alert('Delete failed.'));
    }
});
