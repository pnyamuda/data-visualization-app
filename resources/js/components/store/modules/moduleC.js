import axios from "axios";
import router from "../../../router";

//THIS MODULE MAINLY DEALS WITH THE USER SIDE COMPONENTS.
export const moduleC = {
    state: {
        loggedInUserInfo: "",
        is_admin: "",
        all_questions: [],
        all_categories: [],
        all_surveys: [],
        quiz: "",
        loader: false
    },
    getters: {
        //getting questions with a specific given category_name
        surveyQuestions(state) {
            return id => {
                let stateQuestions = state.all_questions;
                //first getting questions with a category_id equal to the paramater -id.
                let id_questions = stateQuestions.filter(val => {
                    return val.category_id == id;
                });
                if (id_questions.length > 0) {
                    /*Then getting the category_name of one of the question found above. All
                    questions with the same ids have the same category_names*/
                    let cat_name = id_questions[0]["category_name"];
                    /* Then looking for questions that have the same category_name as the question above
                 even though they might have different category_ids. The aim here is to get all questions 
                 that have the same category_name as the filtered one.*/
                    let questions = stateQuestions.filter(val => {
                        return val.category_name == cat_name;
                    });
                    return questions;
                } else {
                    return id_questions;
                }
            };
        }
    },
    mutations: {
        //adding the questions to the state
        loadQuestions(state, paylaod) {
            state.all_questions = paylaod;
            // console.log(state.all_questions);
        },
        //laod category data
        loadCategories(state, payload) {
            state.all_categories = payload;
            //console.log(state.all_categories);
        },
        //load survey list
        loadSurveys(state, payload) {
            state.all_surveys = payload;
        },
        //laod user info
        loadUserInfo(state, payload) {
            state.loggedInUserInfo = payload;
            state.is_admin = payload.is_admin;
        },
        //show/hide the loader
        loaderStatus(state) {
            state.loader = !state.loader;
        }
    },
    actions: {
        //fetching the log in user info.
        getAuthenticatedUserInfo(context) {
            axios.get("/api/user").then(res => {
                context.commit("loadUserInfo", res.data);

                //if the user is successfully logged in
                //getting all the questions that haven't been answered by the user.
                // context.dispatch("getSurveyQuestions");
                //  console.log(context.state.all_questions);
            });
        },
        //fetching all the survey questions
        getSurveyQuestions(context) {
            axios.get("/api/get_questions").then(res => {
                context.commit("loadQuestions", res.data);
                //hide the page loader
                context.commit("loaderStatus");
            });
        },
        //getting quiz categories names for the questions the user hasn't answered
        getCategories(context, payload) {
            axios.get("/api/unanswered_categories").then(res => {
                //getting categories for a specific survey
                let specific_categories = res.data.filter(val => {
                    return val.survey_type_id == payload;
                });
                context.commit("loadCategories", specific_categories);
                //hide the page loader
                context.commit("loaderStatus");
            });
        },
        getSurveys(context) {
            axios.get("/api/unanswered_surveys").then(res => {
                console.log(res.data);
                context.commit("loadSurveys", res.data);
                //hide the page loader
                context.commit("loaderStatus");
            });
        },
        //logging out the admin
        admin_logout(context) {
            axios.post("/api/admin_logout").then(() => {
                router.push({ name: "admin_login" });

                //turn off the loader
                context.commit("loaderStatus");
            });
        },
        //logging out the user
        user_logout(context) {
            axios.post("/api/logout").then(() => {
                router.push({ name: "login" });

                //turn off the loader
                context.commit("loaderStatus");
            });
        }
    }
};
