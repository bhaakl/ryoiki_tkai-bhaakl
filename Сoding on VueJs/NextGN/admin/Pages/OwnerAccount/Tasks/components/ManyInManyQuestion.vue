<template>
   <div class="card">
      <div class="card-header d-flex flex-row justify-content-between">
         <p class="font-weight-bold">Вопрос {{ index }}<span> (с множеством вопросов и несколькими одинаковыми ответами на эти вопросы)</span></p>
          <CButton color="light" size="sm" class="question-drag-btn" v-if="!group_name">
            <CIcon size="sm" name="cil-line-spacing" />
          </CButton>
        <CButton
          v-if="group_name"
          color="info"
          @click="$emit('duplicate-question')"
        >
          Дублировать вопрос
        </CButton>
      </div>
      <div class="card-body">
         <CInputCheckbox
            label="Обязательное комментирование ответа"
            :checked.sync="isCommentRequired"
            name="comment_required"
         />
         <p class="mt-4">Заголовок:</p>
         <VueTextEditor v-model="mutatedQuestionTitle" />
         <p class="mt-4">Вопросы:</p>
         <div class="card-body">
            <div class="card" v-for="(question, questionKey) in $v.questions.$each.$iter">
               <div class="card-body">
                  <template>
                     <div class="form-check form-check-inline" style="margin-bottom: 32px; margin-top: 32px;">
                        <input
                           v-model="question.multipleAnswer.$model"
                           value="single"
                           :name="'multipleAnswer' + questionKey"
                           :id="'multipleAnswerSingle' + questionKey"
                           class="form-check-input"
                           type="radio">
                        <label class="form-check-label" :for="'multipleAnswerSingle' + questionKey" style="margin-right: 1rem;">С одним вариантом ответа</label>
                        <input
                           v-model="question.multipleAnswer.$model"
                           value="multiple"
                           :name="'multipleAnswer' + questionKey"
                           :id="'multipleAnswerMultiple' + questionKey"
                           class="form-check-input"
                           type="radio">
                        <label class="form-check-label" :for="'multipleAnswerMultiple' + questionKey">С несколькими вариантами ответа</label>
                     </div>

                     <p>Описание вопроса:</p>
                     <VueTextEditor v-model="question.value.$model" />
                     <small style="color:red" v-if="question.value.$dirty ? question.value.$error : false">Введите
                        описание</small>
                  </template>

                  <CButton @click="removeQuestion(questionKey)" color="danger" class="float-right mt-4">Удалить вопрос</CButton>
               </div>
            </div>
         </div>

         <CButton @click="addQuestion" class="mt-4" color="success">Добавить вопрос</CButton>

         <p style="margin-top: 1rem;">Варианты ответов:55555555</p>
         <draggable v-model="$v.answers.$model" handle=".answer-drag-btn">
            <template v-for="(answer, answerKey) in $v.answers.$each.$iter">
               <CInput
                  v-model="answer.value.$model"
                  :key="answer.id.$model"
                  placeholder="Вариант"
                  :prepend="`${Number.parseInt(answerKey) + 1}.`"
                  :isValid="answer.value.$dirty ? !answer.value.$error : null"
                  invalidFeedback="Введите вариант"
               >
               </CInput>
              <CInputCheckbox
                :label="`Обязательное комментирование данного ответа`"
                name="stop_tasks"
                style="margin-bottom: 10px; margin-top: -8px; margin-left: 5px;"
                :checked.sync="answer.comment.$model"
              />
            </template>
         </draggable>
         <CButton @click="addAnswer" color="light">Добавить вариант ответа</CButton>
         <CButton @click="$emit('remove-question')" color="danger" class="float-right">Удалить вопрос</CButton>
      </div>
   </div>
</template>

<script>
import { required } from 'vuelidate/lib/validators';
import Draggable from 'vuedraggable';

export default {
   name: 'TasksCreateChooseMany',
   components: {
      Draggable,
   },
   props: {
      index: Number,
      questionId: Number,
      questionTitle: String,
      questionData: {
         type: String,
         default: null,
      },
      values: {
         type: Array,
         default: null,
      },
      comment_required: {
         type: Number,
         default: null,
      },
     group_name: Boolean,
   },
   data() {
      return {
         mutatedQuestionTitle: this.questionTitle || '',
         answers: [
            { id: 1, value: '', comment: false},
            { id: 2, value: '', comment: false},
         ],
         questions: [
            { id: 1, value: '', multipleAnswer: "single" }
         ],
         isCommentRequired: false,
      };
   },
   methods: {
      addAnswer() {
         this.answers.push({ id: this.answers.length + 1, value: '' });
      },
      addQuestion() {
         this.questions.push({ id: this.questions.length + 1, value: '', multipleAnswer: "single" });
      },
      removeQuestion(questionKey) {
         this.questions.splice(questionKey, 1);
      },
      getData() {
         return {
            index: this.index,
            type: 'many',
            question_title: this.mutatedQuestionTitle,
            question: this.questions,
            values: this.answers,
            comment_required: this.isCommentRequired ? 1 : 0,
            id: this.questionId
         };
      },
   },
   validations: {
      answers: {
         $each: {
            id: {},
            value: {
               required,
            },
            comment: {}
         },
      },
      questions: {
         $each: {
            id: {},
            value: {
               required,
            },
            multipleAnswer: {},
         },
      },
   },
   beforeMount() {
      if (this.questionData) {
         this.questions = JSON.parse(this.questionData);
      }
      if (this.values) {
         this.answers = this.values;
      }
      if (this.comment_required) {
         this.isCommentRequired = !!this.comment_required;
      }
   },
};
</script>
