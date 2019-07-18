// TABS
var btnList = document.querySelectorAll('.profile-tabs__control');
var contentList = document.querySelectorAll('.profile-tabs__content');

for (let i = 0; i < btnList.length; i++) {
	btnList[i].addEventListener('click', function () {
		contentList.forEach(element => element.classList.remove('selected'));
		btnList.forEach(element => element.classList.remove('selected'));
		contentList[i].classList.add('selected');
		btnList[i].classList.add('selected');
	})
}
// 
const call = document.querySelector('.call');
const overlay = document.querySelector('.overlay');

// event
window.addEventListener("click", outsideClick);

if (call) {
    call.addEventListener('click', showModal);
}

function showModal() {
	overlay.style.display = "flex";
	document.body.style.overflow = "hidden";

	// Init slider
	const mySiemaWithDots = new SiemaWithDots({
		selector: '.modal__list',
		onInit: function () {
			this.addDots();
			this.updateDots();
		},
		onChange: function () {
			this.updateDots();
			if (mySiemaWithDots.currentSlide === mySiemaWithDots.innerElements.length - 1) {
				document.querySelector('.modal-item__next-slide').style.display = "none";
				document.querySelector('.modal-item__next').style.display = "none";
			} else {
				document.querySelector('.modal-item__next-slide').style.display = "block";
				document.querySelector('.modal-item__next').style.display = "block";
			}
		},
	});

	const next = document.querySelector('.modal-item__next-slide');
	const nextBtn = document.querySelector('.modal-item__next');

	next.addEventListener('click', () => mySiemaWithDots.next());
	nextBtn.addEventListener('click', () => mySiemaWithDots.next());
}

function outsideClick(event) {
	if (event.target === overlay) {
		overlay.style.display = "none";
		document.body.style.overflow = "auto";
	}
}

if (typeof Siema !== 'undefined') {
    class SiemaWithDots extends Siema {
        addDots() {
            this.dots = document.createElement('div');
            this.dots.classList.add('dots');
            for (let i = 0; i < this.innerElements.length; i++) {
                const dot = document.createElement('button');
                dot.classList.add('dots__item');
                dot.addEventListener('click', () => {
                    this.goTo(i);
                });
                this.dots.appendChild(dot);
            }
            this.selector.parentNode.insertBefore(this.dots, this.selector.nextSibling);
        }
        updateDots() {
            for (let i = 0; i < this.dots.querySelectorAll('button').length; i++) {
                const addOrRemove = this.currentSlide === i ? 'add' : 'remove';
                this.dots.querySelectorAll('button')[i].classList[addOrRemove]('dots__item--active');
            }
        }
    }
}
