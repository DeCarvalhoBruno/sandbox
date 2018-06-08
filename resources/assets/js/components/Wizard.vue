<template>
    <div class="wizard">
        <ul class="wizard_steps">
            <li class="wizard_step"
                :class="{
                  'active': isMobile ? currentStep === index : currentStep >= index,
                  'vgw-mobile': isMobile,
                }"
                :style="wizardStepStyle"
                v-for="(step, index) of steps" :key="index">
                <span class="wizard_step_line" :class="{'vgw-mobile': isMobile}"></span>
                <a href.prevent="#" @click="changeStep(index)">
                <span class="wizard_step_label">{{step.label}}</span>
                <span class="wizard_step_indicator"></span>
                    </a>
            </li>
        </ul>
        <span
            class="wizard_arrow"
            :style="{ left: arrowPosition }">
        </span>
        <div ref="wizard-body" class="wizard_body" :class="{'vgw-mobile': isMobile}">
            <div class="wizard_body_step">
                <slot :name="currentSlot"></slot>
            </div>
            <div v-show="!hasStepButtons" class="wizard_body_actions clearfix container p-0">
                <div class="row">
                    <div class="col">
                        <a v-if="backEnabled" class="wizard_back float-left"
                           @click="goBack()">
                            <i class="vgw-icon vgw-prev"></i>
                            <span>Back</span>
                        </a>
                    </div>
                    <div class="col">
                        <span class="wizard-step-number">{{currentStep}}/{{steps.length}}</span>
                    </div>
                    <div class="col">
                        <a v-if="currentStep != steps.length - 1" class="wizard_next float-right"
                           :class="{'disabled': options[currentStep].nextDisabled}"
                           @click="goNext()">
                            <span>Next</span>
                            <i class="vgw-icon vgw-next"></i>
                            <!-- <img src="../images/next.png" alt="next icon"> -->
                        </a>
                        <a v-if="currentStep == steps.length - 1" class="wizard_next float-right final-step"
                           :class="{'disabled': options[currentStep].nextDisabled}"
                           @click="goNext()">
                            Save
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
  export default {
    name: 'wizard',
    props: {
      steps: {},
      hasStepButtons:{default:true},
      onNext:null,
      currentStepParent:Boolean
    },
    watch: {
      steps: {
        handler () {
          this.parseOptions()
        },
        immediate: true
      },
      currentStepParent(){
        this.currentStep = 1
      }
    },
    data () {
      return {
        currentStep: 0,
        isMounted: false,
        resizer: null,
        isMobile: false,
        options: []
      }
    },
    computed: {
      wizardStepStyle () {
        if (this.isMobile) {
          return {
            width: '100%'
          }
        }
        return {
          width: `${100 / this.steps.length}%`
        }
      },
      mobileArrowPosition () {
        return 'calc(50% - 14px)'
      },
      arrowPosition () {
        if (this.isMobile) {
          return this.mobileArrowPosition
        }
        var stepSize = 100 / this.steps.length
        var currentStepStart = stepSize * this.currentStep
        var currentStepMiddle = currentStepStart + (stepSize / 2)
        if (this.steps.length == 1)
          return 'calc(' + currentStepMiddle + '%)'
        else
          return 'calc(' + currentStepMiddle + '% - 10px)'
      },
      currentSlot () {
        return this.steps[this.currentStep].slot
      },
      backEnabled () {
        return this.currentStep != 0
      }
    },
    methods: {
      changeStep(index){
        if(index==0&&this.currentStep!==index)
          this.currentStep=index
      },
      goNext () {
        if (typeof this.onNext == 'function'){
          if(!this.onNext(this.currentStep)) {
            //returned false. don't do anything
            return;
          }
        }
        if (this.currentStep < this.steps.length - 1) {
          this.currentStep++
        }
      },
      goBack () {
        if (this.currentStep > 0) {
          this.currentStep--
        }
      },
      parseOptions () {
        this.options = []
        for (let i = 0; i < this.steps.length; i++) {
          this.options.push(this.steps[i].options ? this.steps[i].options : {})
        }
      },
      handleResize () {
        if (this.resizer) {
          clearTimeout(this.resizer)
        }
        this.resizer = setTimeout(() => {
          this.isMobile = this.$refs['wizard-body'].clientWidth < 400
        }, 100)
      }
    },
    mounted () {
      this.isMobile = this.$refs['wizard-body'].clientWidth < 400
      window.addEventListener('resize', this.handleResize)
    },
    beforeDestroy () {
      window.removeEventListener('resize', this.handleResize)
    }
  }
</script>