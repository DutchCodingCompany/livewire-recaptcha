<script src="https://www.google.com/recaptcha/api.js?render=__SITEKEY__"></script>
<script>
    document.addEventListener('livewire:init', () => {
        Livewire.directive('recaptcha', ({ el, directive, component, cleanup }) => {
            const submitExpression = (() => {
                for(const attr of el.attributes) {
                    if(attr.name.startsWith('wire:submit')) {
                        return attr.value;
                    }
                }
            })();

            const onSubmit = (e) => {
                e.preventDefault();
                e.stopImmediatePropagation();

                grecaptcha.ready(() => {
                    grecaptcha.execute('__SITEKEY__', {action: 'submit'}).then((token) => {
                        component.$wire.$set('gRecaptchaResponse', token).then(() => {
                            Alpine.evaluate(el, "$wire." + submitExpression, { scope: { $event: e } });
                        });
                    });
                });
            }

            el.addEventListener('submit', onSubmit, { capture: true });
        });
    });
</script>
