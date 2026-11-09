{{-- Alpine x-data for a sidebar row with a ⋮ menu (desktop) that also opens
     as a bottom sheet on long-press (touch). Used inside x-data="…" by the
     conversation rows (_conv-item) and the Calls-tab rows, so the two lists
     behave the same. Wire the row with @pointerdown/up/cancel/leave/move. --}}
{
        open: false,
        pressTimer: null,
        startX: 0, startY: 0,
        moved: false,
        pressed: false,
        longPressStart(e) {
            // Touch-only — desktop uses the kebab button, right-click is left alone.
            if (e.pointerType !== 'touch') return;
            this.moved = false;
            this.pressed = true;
            this.startX = e.clientX;
            this.startY = e.clientY;
            const row = e.currentTarget;
            clearTimeout(this.pressTimer);
            this.pressTimer = setTimeout(() => {
                if (this.moved || !this.pressed) return;
                this.open = true;
                if (navigator.vibrate) { try { navigator.vibrate(25); } catch(_){} }
                // Swallow the click that fires on finger-up after a long-press
                // so the row doesn't open the conversation under the menu.
                const stopClick = (ev) => {
                    ev.preventDefault();
                    ev.stopImmediatePropagation();
                };
                row.addEventListener('click', stopClick, { capture: true, once: true });
                setTimeout(() => row.removeEventListener('click', stopClick, true), 700);
            }, 500);
        },
        longPressCancel() {
            this.pressed = false;
            clearTimeout(this.pressTimer);
            this.pressTimer = null;
        },
        trackMove(e) {
            if (!this.pressed) return;
            if (Math.abs(e.clientX - this.startX) > 10 || Math.abs(e.clientY - this.startY) > 10) {
                this.moved = true;
                this.longPressCancel();
            }
        }
    }
